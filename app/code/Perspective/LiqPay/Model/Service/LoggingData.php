<?php
/**
 * LiqPay Extension for Magento 2.
 *
 * @author PerspectiveTeam<order@perspectiveteam.com>
 * © Perspective. All rights reserved
 */
declare(strict_types=1);

namespace Perspective\LiqPay\Model\Service;

use Perspective\LiqPay\Model\Config as PerspectiveConfig;
use Perspective\LiqPay\Sdk\LiqPay;
use Psr\Log\LoggerInterface;

/**
 * IsLiqPayPaymentMethod Class.
 */
class LoggingData
{
    /**
     * @var LoggerInterface
     */
    protected LoggerInterface $logger;

    /**
     * @var LiqPay
     */
    protected LiqPay $liqPay;

    /**
     * @var PerspectiveConfig
     */
    protected PerspectiveConfig $perspectiveConfig;

    /**
     * LoggingData constructor.
     *
     * @param LiqPay $liqPay
     * @param LoggerInterface $logger
     * @param PerspectiveConfig $perspectiveConfig
     */
    public function __construct(
        LiqPay $liqPay,
        LoggerInterface $logger,
        PerspectiveConfig $perspectiveConfig
    ) {
        $this->liqPay = $liqPay;
        $this->logger = $logger;
        $this->perspectiveConfig = $perspectiveConfig;
    }

    /**
     * @param $data
     * @param null|string $signature
     * @return void
     */
    public function execute($data, ?string $signature = ''): void
    {
        $data = $data ?: '';
        $this->logger->info("=====LiqPay Notice: Start of callback!=====");

        $decodedData = $this->liqPay->getDecodedData($data);
        $private = $this->perspectiveConfig->getPrivateKey();

        $signatureEncode = base64_encode(sha1($private . trim($data) . $private, true));
        $decodedData = $decodedData ?: [];
        $this->logger->info('LiqPay Callback Data: ', $decodedData);
        $this->logger->info('Private key: ' . $private);
        $this->logger->info('Signature: ' . $signature);
        $this->logger->info('Data: ' . $data);
        $this->logger->info('Signature2: ' . $signatureEncode);

        $this->logger->info("=====LiqPay Notice: Finish of callback!=====");
    }

}
