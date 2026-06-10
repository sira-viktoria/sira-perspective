<?php
/**
 * LiqPay Extension for Magento 2.
 *
 * @author PerspectiveTeam<order@perspectiveteam.com>
 * © Perspective. All rights reserved
 */
declare(strict_types=1);

namespace Perspective\LiqPay\Model;

use Magento\Checkout\Model\Cart;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Store\Model\ScopeInterface;

/**
 * Config Model.
 */
class Config
{
    /**
     * Paths to Configurations.
     */
    private const XML_PATH_PERSPECTIVE_LIQPAY_ACTIVE = 'payment/perspective_liqpay/active';
    private const XML_PATH_PERSPECTIVE_LIQPAY_SANDBOX = 'payment/perspective_liqpay/sandbox';
    private const XML_PATH_PERSPECTIVE_LIQPAY_TITLE = 'payment/perspective_liqpay/title';
    private const XML_PATH_PERSPECTIVE_LIQPAY_PUBLIC_KEY = 'payment/perspective_liqpay/public_key';
    private const XML_PATH_PERSPECTIVE_LIQPAY_PRIVATE_KEY= 'payment/perspective_liqpay/private_key';
    private const XML_PATH_PERSPECTIVE_LIQPAY_TEST_ORDER_SUFFIX = 'payment/perspective_liqpay/sandbox_order_suffix';
    private const XML_PATH_PERSPECTIVE_LIQPAY_DESCRIPTION = 'payment/perspective_liqpay/description';
    private const XML_PATH_PERSPECTIVE_LIQPAY_SECURITY_CHECK= 'payment/perspective_liqpay/security_check';

    /**
     * @var ScopeConfigInterface
     */
    private ScopeConfigInterface $scopeConfig;

    /**
     * @var SerializerInterface
     */
    private SerializerInterface $serializer;

    /**
     * Config constructor.
     *
     * @param ScopeConfigInterface $scopeConfig
     * @param SerializerInterface $serializer
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        SerializerInterface  $serializer
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->serializer = $serializer;
    }

    /**
     * @return bool
     */
    public function isEnabled(): bool
    {
        return $this->scopeConfig->isSetFlag(self::XML_PATH_PERSPECTIVE_LIQPAY_ACTIVE, ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return bool
     */
    public function isTestMode(): bool
    {
        return $this->scopeConfig->isSetFlag(self::XML_PATH_PERSPECTIVE_LIQPAY_SANDBOX, ScopeInterface::SCOPE_STORE);
    }

    public function getPublicKey()
    {
        $var = $this->scopeConfig->getValue(
            self::XML_PATH_PERSPECTIVE_LIQPAY_PUBLIC_KEY,
            ScopeInterface::SCOPE_STORE
        );

        return isset($var) ? trim($var): '';
    }

    public function getPrivateKey()
    {
        $var = $this->scopeConfig->getValue(
            self::XML_PATH_PERSPECTIVE_LIQPAY_PRIVATE_KEY,
            ScopeInterface::SCOPE_STORE
        );

        return isset($var) ? trim($var): '';
    }

    public function getTestOrderSuffix()
    {
        $var = $this->scopeConfig->getValue(
            self::XML_PATH_PERSPECTIVE_LIQPAY_TEST_ORDER_SUFFIX,
            ScopeInterface::SCOPE_STORE
        );

        return isset($var) ? trim($var): '';
    }

    /**
     * @return bool
     */
    public function isSecurity(): bool
    {
        return $this->scopeConfig->isSetFlag(self::XML_PATH_PERSPECTIVE_LIQPAY_SECURITY_CHECK, ScopeInterface::SCOPE_STORE);
    }

    /**
     * @param \Magento\Sales\Api\Data\OrderInterface|null $order
     * @return string
     */
    public function getLiqPayDescription(?\Magento\Sales\Api\Data\OrderInterface $order = null)
    {

        $var = $this->scopeConfig->getValue(
            self::XML_PATH_PERSPECTIVE_LIQPAY_DESCRIPTION,
            ScopeInterface::SCOPE_STORE
        );
        $description = isset($var) ? trim($var): '';

        $params = [
            '{order_id}' => $order->getIncrementId(),
        ];
        return strtr($description, $params);
    }


}
