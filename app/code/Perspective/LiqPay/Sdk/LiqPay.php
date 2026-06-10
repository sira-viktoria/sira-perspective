<?php
/**
 * LiqPay Extension for Magento 2.
 *
 * @author PerspectiveTeam<order@perspectiveteam.com>
 * © Perspective. All rights reserved
 */
declare(strict_types=1);

namespace Perspective\LiqPay\Sdk;

use Perspective\LiqPay\Model\Config;

/** Extends official LiqPay Sdk */
class LiqPay extends \Perspective\LiqPay\Sdk\LiqPay\LiqPay
{
    const string VERSION = '3';
    const string TEST_MODE_SUFFIX_DELIM = '-';
    const string STATUS_SUCCESS = 'success';
    const string STATUS_WAIT_COMPENSATION = 'wait_compensation';
    const string STATUS_SUBSCRIBED = 'subscribed';
    const string STATUS_WAIT_RESERVE = 'wait_reserve';
    const string STATUS_PROCESSING = 'processing';
    const STATUS_FAILURE = 'failure';
    const string STATUS_ERROR = 'error';
    const string STATUS_WAIT_SECURE = 'wait_secure';
    const STATUS_WAIT_ACCEPT = 'wait_accept';
    const string STATUS_WAIT_CARD = 'wait_card';
    const string STATUS_HOLD_WAIT = 'hold_wait';
    const string STATUS_REVERSED = 'reversed';
    const string STATUS_SANDBOX = 'sandbox';

    /**
     * @var string[]
     */
    public $_supportedCurrencies = [
        'AUD',
        'CAD',
        'CZK',
        'DKK',
        'EUR',
        'HKD',
        'HUF',
        'ILS',
        'JPY',
        'MXN',
        'NOK',
        'NZD',
        'PLN',
        'GBP',
        'RUB',
        'SGD',
        'SEK',
        'CHF',
        'TWD',
        'THB',
        'USD',
        'INR',
    ] {
        get {
            return $this->_supportedCurrencies;
        }
    }

    /**
     * @var Config
     */
    protected Config $perspectiveConfig;

    /**
     * @param Config $perspectiveConfig
     */
    public function __construct(
        Config $perspectiveConfig
    ) {
        $this->perspectiveConfig = $perspectiveConfig;
        if ($perspectiveConfig->isEnabled()) {
            $publicKey = $perspectiveConfig->getPublicKey();
            $privateKey = $perspectiveConfig->getPrivateKey();
            parent::__construct($publicKey, $privateKey);
        }
    }

    /**
     * @param $params
     * @return mixed
     */
    protected function prepareParams($params)
    {
        if (!isset($params['sandbox'])) {
            $params['sandbox'] = (int)$this->perspectiveConfig->isTestMode();
        }
        if (!isset($params['version'])) {
            $params['version'] = static::VERSION;
        }
        if (isset($params['order_id']) && $this->perspectiveConfig->isTestMode()) {
            $suffix = $this->perspectiveConfig->getTestOrderSuffix();
            if (!empty($suffix)) {
                $params['order_id'] .= self::TEST_MODE_SUFFIX_DELIM . $suffix;
            }
        }
        return $params;
    }

    /**
     * @param $path
     * @param $params
     * @param $timeout
     * @return mixed|string[]
     */
    public function api($path, $params = array(), $timeout = 5): mixed
    {
        $params = $this->prepareParams($params);

        return parent::api($path, $params, $timeout);
    }

    /**
     * @param $params
     * @return string
     */
    public function cnb_form($params): string
    {
        $params = $this->prepareParams($params);
        return parent::cnb_form($params);
    }

    /**
     * @param $data
     * @return mixed
     */
    public function getDecodedData($data): mixed
    {
        $data = $data ?: '';
        return json_decode(base64_decode($data), true, 1024);
    }

    /**
     * @param $signature
     * @param $data
     * @return bool
     */
    public function checkSignature($signature, $data): bool
    {
        $privateKey = $this->perspectiveConfig->getPrivateKey();
        $generatedSignature = base64_encode(sha1($privateKey . $data . $privateKey, 1));

        return $signature == $generatedSignature;
    }
}
