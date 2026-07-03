<?php
declare(strict_types=1);

namespace Perspective\CustomerProductInfoGraphQl\Model\Resolver;

use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Perspective\CustomerProductInfoGraphQl\Service\CurrentCustomer;
use Perspective\CustomerProductInfoGraphQl\Service\CustomerProductOrders;

/**
 * CustomerProductInfo Class.
 */
class CustomerProductInfo implements ResolverInterface
{
    /**
     * @var CurrentCustomer
     */
    protected CurrentCustomer $currentCustomerService;

    /**
     * @var CustomerProductOrders
     */
    protected CustomerProductOrders $customerProductOrdersService;

    /**
     * @param CurrentCustomer $currentCustomerService
     * @param CustomerProductOrders $customerProductOrdersService
     */
    public function __construct(
        CurrentCustomer $currentCustomerService,
        CustomerProductOrders $customerProductOrdersService
    ) {
        $this->currentCustomerService = $currentCustomerService;
        $this->customerProductOrdersService = $customerProductOrdersService;
    }

    /**
     * @param Field $field
     * @param $context
     * @param ResolveInfo $info
     * @param array|null $value
     * @param array|null $args
     * @return array
     */
    public function resolve(
        Field       $field,
                    $context,
        ResolveInfo $info,
        ?array      $value = null,
        ?array      $args = null
    ): array {
        return $this->collectData($info, $context, $args);
    }

    /**
     * Prepare data for graphql response.
     *
     * @param ResolveInfo $info
     * @param $context
     * @param array $args
     * @return array
     */
    private function collectData(ResolveInfo $info, $context, array $args): array
    {
        $this->currentCustomerService->setCustomerId($context->getUserId());
        $this->customerProductOrdersService->setProductId($args['productId']);

        $fieldSelection = $info->getFieldSelection();

        $isCustomerLoggedIn = $this->currentCustomerService->isCustomerLoggedIn();
        $data = [
            'customerIsLoggedIn' => $isCustomerLoggedIn,
        ];

        if (!$isCustomerLoggedIn) {
            return array_merge($data, [
                'hasPurchased'     => false,
                'lastPurchaseDate' => '',
                'ordersCount'      => 0,
                'customerGroup'    => 'Guest',
                'customText'       => ''
            ]);
        }

        if (!empty($fieldSelection['customText'])) {
            $data['customText'] = __('We recommend this item!');
        }

        if (!empty($fieldSelection['customerGroup'])) {
            $data['customerGroup'] = $this->currentCustomerService->getCustomerGroupName();
        }

        if (!empty($fieldSelection['hasPurchased'])) {
            $data['hasPurchased'] = $this->customerProductOrdersService->isCustomerOrderedProduct();
        }

        if (!empty($fieldSelection['lastPurchaseDate'])) {
            $data['lastPurchaseDate'] = $this->customerProductOrdersService->getLastPurchaseDate();
        }

        if (!empty($fieldSelection['ordersCount'])) {
            $data['ordersCount'] = $this->customerProductOrdersService->getProductOrdersCount();
        }
        return $data;
    }
}
