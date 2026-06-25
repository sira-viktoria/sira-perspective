<?php
declare(strict_types=1);

namespace Perspective\ErpImport\Service;

use Magento\Framework\HTTP\Client\Curl;
use Magento\Integration\Api\AdminTokenServiceInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Encryption\EncryptorInterface;

/**
 * BulkManager Class.
 */
class BulkManager
{
    /**
     * @var Curl
     */
    protected $curl;
    /**
     * @var AdminTokenServiceInterface
     */
    protected $adminTokenService;
    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;
    /**
     * @var EncryptorInterface
     */
    protected $encryptor;
    /**
     * @param Curl $curl
     * @param AdminTokenServiceInterface $adminTokenService
     * @param ScopeConfigInterface $scopeConfig
     * @param EncryptorInterface $encryptor
     */
    public function __construct(
        Curl $curl,
        AdminTokenServiceInterface $adminTokenService,
        ScopeConfigInterface $scopeConfig,
        EncryptorInterface $encryptor
    ) {
        $this->curl = $curl;
        $this->adminTokenService = $adminTokenService;
        $this->scopeConfig = $scopeConfig;
        $this->encryptor = $encryptor;
    }

    /**
     * Send bulk request
     *
     * @param $batch //rows array
     * @return array //[response_body, response_status]
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function process($batch)
    {
        //send request
        $url = 'https://app.magento.test/rest/default/async/bulk/V1/products';
        $this->curl->setHeaders([
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $this->getAdminToken()
        ]);
        $this->curl->post($url, $this->buildBulkPayload($batch));

        //delete admin token
        $this->adminTokenService->revokeAdminAccessToken(1);

        //get request response data
        $response = $this->curl->getBody();
        $httpStatus = $this->curl->getStatus();
        return ['body' => json_decode($response, true),
                'status' => $httpStatus];
    }


    /**
     * Build request body from array
     *
     * @param array $items
     * @return string
     */
    private function buildBulkPayload(array $items): string
    {
        $payload = [];

        foreach ($items as $item) {
            $payload[] = [
                'product' => [
                    'sku' => $item['sku'],
                    'price' => (float)$item['price'],
                    'status' => (int)$item['status']
                ]
            ];
        }
        return json_encode($payload);
    }

    /**
     * Create admin token
     *
     * @return string
     * @throws \Magento\Framework\Exception\AuthenticationException
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function getAdminToken()
    {
        $admin = $this->scopeConfig->getValue('erp_import/admin_settings');
        return $this->adminTokenService->createAdminAccessToken($admin['nickname'], $admin['password']);
    }
}
