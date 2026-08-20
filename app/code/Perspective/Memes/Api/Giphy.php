<?php
declare(strict_types=1);

namespace Perspective\Memes\Api;

use Exception;
use Perspective\Memes\Service\Config;
use Magento\Framework\HTTP\Client\Curl;
use Psr\Log\LoggerInterface;
use Perspective\Memes\Service\MemeSearchWord;
use Perspective\Memes\Exception\GiphyApiException;

/**
 * Api Giphy.
 */
class Giphy
{
    /**
     * @var Config
     */
    protected Config $configDataService;

    /**
     * @var Curl
     */
    protected Curl $curl;

    /**
     * @var LoggerInterface
     */
    protected LoggerInterface $logger;

    /**
     * @var MemeSearchWord
     */
    protected MemeSearchWord $memeSearchWordService;

    /**
     * Giphy constructor.
     *
     * @param Config $configDataService
     * @param Curl $curl
     * @param LoggerInterface $logger
     * @param MemeSearchWord $memeSearchWordService
     */
    public function __construct(
        Config $configDataService,
        Curl $curl,
        LoggerInterface $logger,
        MemeSearchWord $memeSearchWordService
    ) {
        $this->configDataService = $configDataService;
        $this->curl = $curl;
        $this->logger = $logger;
        $this->memeSearchWordService = $memeSearchWordService;
    }

    /**
     * Send request to Giphy API
     *
     * @param int $entityId
     * @param string $action
     * @return array[]
     */
    public function request(int $entityId, string $action = ''): array
    {
        try {
            $q = $this->memeSearchWordService->getSearchWordForQuote($entityId, $action);

            $limit = $this->configDataService->getGifsCount();
            $apiKey = $this->configDataService->getGiphyApiKey();
            $apiUrl = $this->configDataService->getGiphyApiUrl();

            $url = sprintf('%s?api_key=%s&q=%s&limit=%s', $apiUrl, $apiKey, $q, $limit);

            $this->curl->get($url);
            $response = json_decode($this->curl->getBody(), true);

            if (!isset($response['meta']['status']) || $response['meta']['status'] != 200) {
                throw new GiphyApiException(__('Giphy API returned invalid status: %1', $response['meta']['status'] ?? 'null'));
            }
            if (empty($response['data'])) {
                throw new GiphyApiException(__('Giphy API returned empty or invalid data'));
            }

        } catch (Exception $e) {
            $this->logger->error(__('Giphy API request failed. %1', $e->getMessage()));
            $response = ['data' => []];
        }
        return $response;
    }

    /**
     * Get images url from response array.
     *
     * @param int $entityId
     * @param string $action
     * @return array
     */
    public function getImagesUrl(int $entityId, string $action = ''): array
    {
        $response = $this->request($entityId, $action);

        $result = [];
        foreach ($response['data'] as $item) {
            if (isset($item['images']['fixed_height']['url'])) {
                $result[] = $item['images']['fixed_height']['url'];
            }
        }
        return $result;
    }
}
