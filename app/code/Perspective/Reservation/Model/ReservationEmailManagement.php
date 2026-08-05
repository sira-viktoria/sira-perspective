<?php
declare(strict_types=1);

namespace Perspective\Reservation\Model;

use Perspective\Reservation\Api\ReservationEmailManagementInterface;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Framework\Translate\Inline\StateInterface;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface;

/**
 * Send Email Manager.
 */
class ReservationEmailManagement implements ReservationEmailManagementInterface
{
    private const string XML_PATH_CUSTOMER_TEMPLATE = 'sales/reservation_general/reservation_customer_template';
    private const string XML_PATH_ADMIN_TEMPLATE = 'sales/reservation_general/reservation_admin_template';
    private const string XML_PATH_CANCELED_TEMPLATE = 'sales/reservation_general/reservation_canceled_template';

    /**
     * @var StateInterface
     */
    protected StateInterface $inlineTranslation;

    /**
     * @var TransportBuilder
     */
    protected TransportBuilder $transportBuilder;

    /**
     * @var ScopeConfigInterface
     */
    protected ScopeConfigInterface $scopeConfig;

    /**
     * @var StoreManagerInterface
     */
    protected StoreManagerInterface $storeManager;

    /**
     * @var LoggerInterface
     */
    protected LoggerInterface $logger;

    /**
     * ReservationEmailManagement constructor.
     *
     * @param StateInterface $inlineTranslation
     * @param TransportBuilder $transportBuilder
     * @param ScopeConfigInterface $scopeConfig
     * @param StoreManagerInterface $storeManager
     * @param LoggerInterface $logger
     */
    public function __construct(
        StateInterface $inlineTranslation,
        TransportBuilder $transportBuilder,
        ScopeConfigInterface $scopeConfig,
        StoreManagerInterface $storeManager,
        LoggerInterface $logger
    ) {
        $this->inlineTranslation = $inlineTranslation;
        $this->transportBuilder = $transportBuilder;
        $this->scopeConfig = $scopeConfig;
        $this->storeManager = $storeManager;
        $this->logger = $logger;
    }

    /**
     * @param OrderInterface $order
     * @param string $customerEmail
     * @param string $untilDate
     *
     * @return void
     */
    public function sendReservationEmails(OrderInterface $order, string $customerEmail, string $untilDate): void
    {
        $this->inlineTranslation->suspend();
        try {
            $storeId = $this->storeManager->getStore()->getId();
            $sender = $this->getSenderData();

            $customerTemplate = $this->scopeConfig->getValue(self::XML_PATH_CUSTOMER_TEMPLATE, 'store', $storeId);
            $adminTemplate = $this->scopeConfig->getValue(self::XML_PATH_ADMIN_TEMPLATE, 'store', $storeId);

            $transportCustomer = $this->transportBuilder
                ->setTemplateIdentifier($customerTemplate)
                ->setTemplateOptions(['area' => 'frontend', 'store' => $storeId])
                ->setTemplateVars(['order' => $order, 'until_date' => $untilDate])
                ->setFromByScope($sender, $storeId)
                ->addTo($customerEmail)
                ->getTransport();
            $transportCustomer->sendMessage();

            $transportAdmin = $this->transportBuilder
                ->setTemplateIdentifier($adminTemplate)
                ->setTemplateOptions(['area' => 'frontend', 'store' => $storeId])
                ->setTemplateVars(['order' => $order, 'until_date' => $untilDate])
                ->setFromByScope($sender, $storeId)
                ->addTo($sender['email'])
                ->getTransport();
            $transportAdmin->sendMessage();

        } catch (\Exception $e) {
            $this->logger->error('Reservation Email Error (Creation): ' . $e->getMessage());
        }

        $this->inlineTranslation->resume();
    }

    /**
     * @param OrderInterface $order
     *
     * @return void
     */
    public function sendCancellationEmail(OrderInterface $order): void
    {
        $this->inlineTranslation->suspend();
        try {
            $storeId = $this->storeManager->getStore()->getId();
            $sender = $this->getSenderData();
            $canceledTemplate = $this->scopeConfig->getValue(self::XML_PATH_CANCELED_TEMPLATE, 'store', $storeId);

            $transport = $this->transportBuilder
                ->setTemplateIdentifier($canceledTemplate)
                ->setTemplateOptions(['area' => 'frontend', 'store' => $storeId])
                ->setTemplateVars(['order' => $order])
                ->setFromByScope($sender, $storeId)
                ->addTo($order->getCustomerEmail())
                ->getTransport();
            $transport->sendMessage();

        } catch (\Exception $e) {
            $this->logger->error('Reservation Email Error (Cancellation): ' . $e->getMessage());
        }
        $this->inlineTranslation->resume();
    }

    /**
     * @return array
     */
    public function getSenderData(): array
    {
        $senderEmail = $this->scopeConfig->getValue('trans_email/ident_general/email');
        $senderName = $this->scopeConfig->getValue('trans_email/ident_general/name') ?: 'Store Admin';

        return ['name' => $senderName, 'email' => $senderEmail];
    }
}
