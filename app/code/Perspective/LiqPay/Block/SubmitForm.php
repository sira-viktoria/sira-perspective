<?php
/**
 * LiqPay Extension for Magento 2.
 *
 * @author PerspectiveTeam<order@perspectiveteam.com>
 * © Perspective. All rights reserved
 */
declare(strict_types=1);

namespace Perspective\LiqPay\Block;

use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\Template;
use Magento\Sales\Model\Order;
use Perspective\LiqPay\Model\Config as PerspectiveConfig;
use Perspective\LiqPay\Sdk\LiqPay;

/**
 * SubmitForm Clock Class.
 */
class SubmitForm extends Template
{
    /**
     * @var Order|null
     */
    protected ?Order $order = null;

    /* @var $liqPay LiqPay */
    protected LiqPay $liqPay;

    /**
     * @var PerspectiveConfig
     */
    protected PerspectiveConfig $perspectiveConfig;
    /**
     * @var UrlInterface
     */
    private UrlInterface $urlBuilder;
    /**
     * SubmitForm constructor.
     *
     * @param Template\Context $context
     * @param LiqPay $liqPay
     * @param PerspectiveConfig $perspectiveConfig
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        LiqPay $liqPay,
        PerspectiveConfig $perspectiveConfig,
        UrlInterface $urlBuilder,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->liqPay = $liqPay;
        $this->perspectiveConfig = $perspectiveConfig;
        $this->urlBuilder = $urlBuilder;
    }

    /**
     * @return Order|null
     * @throws \Exception
     */
    public function getOrder(): ?Order
    {
        if ($this->order === null) {
            throw new \Exception('Order is not set');
        }
        return $this->order;
    }

    /**
     * @param Order $order
     * @return void
     */
    public function setOrder(Order $order): void
    {
        $this->order = $order;
    }

    /**
     * @return string
     * @throws \Exception
     */
    protected function _toHtml(): string
    {
        $order = $this->getOrder();
        return $this->liqPay->cnb_form(array(
            'action' => 'pay',
            'amount' => $order->getGrandTotal(),
            'currency' => $order->getOrderCurrencyCode(),
            'description' => $this->perspectiveConfig->getLiqPayDescription($order),
            'order_id' => $order->getIncrementId(),
//            'result_url' => 'https://app.magento2.test/checkout/onepage/success',
//            'server_urls' => 'http://app.magento2.test/rest/V1/liqpay/callback',
            'result_url' =>$this->urlBuilder->getUrl('checkout/onepage/success/'),
            'server_url' => $this->urlBuilder->getUrl('rest/V1/liqpay/').'callback',



        ));
    }
}
