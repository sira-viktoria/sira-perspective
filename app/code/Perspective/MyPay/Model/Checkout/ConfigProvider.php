<?php
/**
 * Viktoriia Sira <viktoriia.s@perspectiveteam.com>
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Perspective\MyPay\Model\Checkout;

use Magento\Checkout\Model\ConfigProviderInterface;
use Magento\Framework\Escaper;
use Magento\Framework\Exception\LocalizedException;
use Magento\Payment\Helper\Data as PaymentHelper;
use Magento\Payment\Model\MethodInterface;

/**
 * Configuration provider for MyPay rendering.
 */
class ConfigProvider implements ConfigProviderInterface
{
    /**
     * @var string[]
     */
    protected string|array $methodCode = 'perspectivemypay';

    /**
     * @var MethodInterface
     */
    protected MethodInterface $method;

    /**
     * @var Escaper
     */
    protected Escaper $escaper;

    /**
     * ConfigProvider constructor.
     * @param PaymentHelper $paymentHelper
     * @param Escaper $escaper
     * @throws LocalizedException
     */
    public function __construct(
        PaymentHelper $paymentHelper,
        Escaper $escaper
    ) {
        $this->escaper = $escaper;
        $this->method = $paymentHelper->getMethodInstance($this->methodCode);
    }

    /**
     * {@inheritdoc}
     */
    public function getConfig()
    {
        return $this->method->isAvailable() ? [
            'payment' => [
                'perspectivemypay' => [
                    'mailingAddress' => $this->getMailingAddress()
                ],
            ],
        ] : [];
    }

    /**
     * Get mailing address from config
     *
     * @return string
     */
    protected function getMailingAddress(): string
    {
        return nl2br($this->escaper->escapeHtml($this->method->getMailingAddress()));
    }
}
