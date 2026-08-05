<?php
declare(strict_types=1);

namespace Perspective\Reservation\Model;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Customer\Api\Data\AddressInterface;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Payment\Model\MethodInterface;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\Quote\Address;
use Perspective\Reservation\Api\ReservationManagementInterface;
use Perspective\Reservation\Api\ReservationEmailManagementInterface;
use Magento\Quote\Model\QuoteFactory;
use Magento\Quote\Model\QuoteManagement;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Api\Data\PaymentInterfaceFactory;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\DataObjectFactory;
use Magento\Sales\Api\OrderRepositoryInterface;

/**
 * ReservationManagement Class.
 */
class ReservationManagement implements ReservationManagementInterface
{
    /**
     * @var QuoteFactory
     */
    protected QuoteFactory $quoteFactory;

    /**
     * @var QuoteManagement
     */
    protected QuoteManagement $quoteManagement;

    /**
     * @var CartRepositoryInterface
     */
    protected CartRepositoryInterface $cartRepository;

    /**
     * @var PaymentInterfaceFactory
     */
    protected PaymentInterfaceFactory $paymentFactory;

    /**
     * @var ProductRepositoryInterface
     */
    protected ProductRepositoryInterface $productRepository;

    /**
     * @var CustomerRepositoryInterface
     */
    protected CustomerRepositoryInterface $customerRepository;

    /**
     * @var StoreManagerInterface
     */
    protected StoreManagerInterface $storeManager;

    /**
     * @var ReservationEmailManagementInterface
     */
    protected ReservationEmailManagementInterface $emailManagement;

    /**
     * @var DataObjectFactory
     */
    protected DataObjectFactory $dataObjectFactory;

    /**
     * @var OrderRepositoryInterface
     */
    protected OrderRepositoryInterface $orderRepository;

    /**
     * ReservationManagement constructor.
     *
     * @param QuoteFactory $quoteFactory
     * @param QuoteManagement $quoteManagement
     * @param CartRepositoryInterface $cartRepository
     * @param PaymentInterfaceFactory $paymentFactory
     * @param ProductRepositoryInterface $productRepository
     * @param CustomerRepositoryInterface $customerRepository
     * @param StoreManagerInterface $storeManager
     * @param ReservationEmailManagementInterface $emailManagement
     * @param DataObjectFactory $dataObjectFactory
     * @param OrderRepositoryInterface $orderRepository
     */
    public function __construct(
        QuoteFactory $quoteFactory,
        QuoteManagement $quoteManagement,
        CartRepositoryInterface $cartRepository,
        PaymentInterfaceFactory $paymentFactory,
        ProductRepositoryInterface $productRepository,
        CustomerRepositoryInterface $customerRepository,
        StoreManagerInterface $storeManager,
        ReservationEmailManagementInterface $emailManagement,
        DataObjectFactory $dataObjectFactory,
        OrderRepositoryInterface $orderRepository
    ) {
        $this->quoteFactory = $quoteFactory;
        $this->quoteManagement = $quoteManagement;
        $this->cartRepository = $cartRepository;
        $this->paymentFactory = $paymentFactory;
        $this->productRepository = $productRepository;
        $this->customerRepository = $customerRepository;
        $this->storeManager = $storeManager;
        $this->emailManagement = $emailManagement;
        $this->dataObjectFactory = $dataObjectFactory;
        $this->orderRepository    = $orderRepository;
    }

    /**
     * @param array $data
     *
     * @return OrderInterface
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function createReservationOrder(array $data): OrderInterface
    {
        $store = $this->storeManager->getStore();
        $product = $this->getProductFromData($data);
        $quote = $this->createQuote($store);
        $customerAddress = $this->assignCustomerToQuote($quote, $data);
        $this->addProductToQuote($quote, $product, $data);
        $shippingAddress = $this->prepareAddresses($quote, $customerAddress, $data);
        $this->setShippingMethod($shippingAddress);
        $this->setPaymentMethod($quote);
        $this->collectAndSaveQuote($quote);
        $order = $this->createOrderFromQuote($quote);
        $this->applyReservationToOrder($order);
        $this->sendReservationEmails($order, $data);

        return $order;
    }

    /**
     * @param array $data
     *
     * @return ProductInterface
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    private function getProductFromData(array $data): ProductInterface
    {
        $productId = $data['product'] ?? $data['product_id'] ?? null;
        if (!$productId) {
            throw new LocalizedException(__('No product ID was found for the reservation.'));
        }

        return $this->productRepository->getById((int)$productId);
    }

    /**
     * @param $store
     *
     * @return Quote
     */
    private function createQuote($store): Quote
    {
        $quote = $this->quoteFactory->create();
        $quote->setStore($store);
        $quote->setCurrency();

        return $quote;
    }

    /**
     * @param Quote $quote
     * @param array $data
     *
     * @return AddressInterface|mixed|null
     */
    private function assignCustomerToQuote(Quote $quote, array $data): mixed
    {
        $customerAddress = null;
        try {
            $customer = $this->customerRepository->get($data['email']);
            $quote->assignCustomer($customer);
            $defaultShippingId = $customer->getDefaultShipping();

            if ($defaultShippingId) {
                foreach ($customer->getAddresses() as $address) {
                    if ($address->getId() == $defaultShippingId) {
                        $customerAddress = $address;
                        break;
                    }
                }
            }
        } catch (\Exception $e) {
             $quote->setCustomerEmail($data['email'] ?? '');
             $quote->setCustomerFirstname($data['name'] ?? 'Guest');
             if (!empty($data['telephone'])) {
                 $quote->setCustomAttribute('telephone', $data['telephone']);
             }

             $quote->setCustomerIsGuest(true);
        }

        return $customerAddress;
    }

    /**
     * @param Quote $quote
     * @param ProductInterface $product
     * @param array $data
     *
     * @return void
     * @throws LocalizedException
     */
    private function addProductToQuote(Quote $quote, ProductInterface $product, array $data): void
    {
        $buyRequest = $this->dataObjectFactory->create(['data' => $data]);
        $quoteItem = $quote->addProduct($product, $buyRequest);
        if (is_string($quoteItem)) {
            throw new LocalizedException(__($quoteItem));
        }
    }

    /**
     * @param Quote $quote
     * @param AddressInterface|null $customerAddress
     * @param array $data
     *
     * @return DataObject|Address
     */
    private function prepareAddresses(Quote $quote, ?AddressInterface $customerAddress, array $data): DataObject|Address
    {
        if ($customerAddress && $customerAddress->getId()) {
            $quote->getBillingAddress()->importCustomerAddressData($customerAddress);
            $shippingAddress = $quote->getShippingAddress()->importCustomerAddressData($customerAddress);
            if (!empty($data['telephone'])) {
                $quote->getBillingAddress()->setTelephone($data['telephone']);
                $shippingAddress->setTelephone($data['telephone']);
            }
        } else {
            $nameParts = preg_split('/\s+/', trim((string)($data['name'] ?? 'Guest Guest')), 2);
            $addressData = [
                'firstname' => $nameParts[0] ?? 'Guest',
                'lastname' => $nameParts[1] ?? 'Guest',
                'street' => ['Pickup'],
                'city' => 'Local Store',
                'country_id' => 'UA',
                'region' => 'Kyiv',
                'region_id' => 0,
                'telephone' => $data['telephone'] ?? '',
                'postcode' => '00000'
            ];
            $quote->getBillingAddress()->addData($addressData)->setRegion('Kyiv');
            $shippingAddress = $quote->getShippingAddress()->addData($addressData)->setRegion('Kyiv');
        }

        return $shippingAddress;
    }


    /**
     * @param $shippingAddress
     * @return void
     */
    private function setShippingMethod($shippingAddress): void
    {
        $shippingAddress->setShippingMethod('freeshipping_freeshipping');
        $shippingAddress->setCollectShippingRates(true)->collectShippingRates();

    }

    /**
     * @param Quote $quote
     *
     * @return void
     */
    private function setPaymentMethod(Quote $quote): void
    {
        $quote->getPayment()->setMethod('checkmo');
        $quote->getPayment()->setChecks([
            MethodInterface::CHECK_USE_FOR_COUNTRY,
            MethodInterface::CHECK_USE_FOR_CURRENCY,
            MethodInterface::CHECK_USE_CHECKOUT
        ]);
    }

    /**
     * @param Quote $quote
     *
     * @return void
     */
    private function collectAndSaveQuote(Quote $quote): void
    {
        $quote->collectTotals();
        $this->cartRepository->save($quote);
    }

    /**
     * @param Quote $quote
     *
     * @return OrderInterface
     * @throws LocalizedException
     */
    private function createOrderFromQuote(Quote $quote): OrderInterface
    {
        $order = $this->quoteManagement->submit($quote);
        if (!$order) {
            throw new LocalizedException(__('The reservation request could not be created.'));
        }

        return $order;
    }

    /**
     * @param OrderInterface $order
     *
     * @return void
     */
    private function applyReservationToOrder(OrderInterface $order): void
    {
        $order->setStatus('reservation');
        $order->setState(\Magento\Sales\Model\Order::STATE_NEW);
        $order->setIsReservation(1);
        $untilDate = date('Y-m-d H:i:s', strtotime('+24 hours'));
        $comment = __("Reserved until %1", $untilDate);
        $order->addCommentToStatusHistory($comment, 'reservation', true);
        $this->orderRepository->save($order);
    }

    /**
     * @param OrderInterface $order
     * @param array $data
     *
     * @return void
     */
    private function sendReservationEmails(OrderInterface $order, array $data): void
    {
        $untilDate = date('Y-m-d H:i:s', strtotime('+24 hours'));
        $this->emailManagement->sendReservationEmails($order, $data['email'], $untilDate);
    }
}
