<?php
declare(strict_types=1);

namespace Perspective\Memes\Model\Memes;

use Perspective\Memes\Api\Giphy;
use Perspective\Memes\Service\Config;
use Perspective\Memes\Service\MemeSearchWord;
use Psr\Log\LoggerInterface;

class MemeManager
{
    public ?string $selectedMeme = '';

    /**
     * @var Giphy
     */
    protected Giphy $giphyApi;

    /**
     * @var MemeDataHandler
     */
    protected MemeDataHandler $memeDataHandler;

    /**
     * @var MemeSearchWord
     */
    protected MemeSearchWord $memeSearchWordService;

    /**
     * @var Config
     */
    protected Config $configDataService;

    /**
     * @var LoggerInterface
     */
    protected LoggerInterface $logger;

    /**
     * MemeManager constructor.
     *
     * @param Giphy $giphyApi
     * @param MemeDataHandler $memeDataHandler
     * @param MemeSearchWord $memeSearchWordService
     * @param Config $configDataService
     * @param LoggerInterface $logger
     */
    public function __construct(
        Giphy $giphyApi,
        MemeDataHandler $memeDataHandler,
        MemeSearchWord $memeSearchWordService,
        Config $configDataService,
        LoggerInterface $logger,
    ) {
        $this->giphyApi = $giphyApi;
        $this->memeDataHandler = $memeDataHandler;
        $this->memeSearchWordService = $memeSearchWordService;
        $this->configDataService = $configDataService;
        $this->logger = $logger;
    }

    public function getData(int $entityId, string $entityType): array
    {
        if (!$this->configDataService->isModuleEnabled()) {
            return [];
        }

        if (!$this->memeDataHandler->hasMemes($entityId, $entityType)) {

            try {
                $memesUrlArray = $this->giphyApi->getImagesUrl($entityId);
                $this->memeDataHandler->saveMemes($entityId, $entityType, $memesUrlArray, $this->selectedMeme );
            } catch (\Exception $e) {
                $this->logger->critical($e->getMessage());
            }
        }
        return $this->memeDataHandler->getMemes($entityId, $entityType);
    }

    /**
     * Update selected meme for entity
     *
     * @param int $entityId
     * @param string $entityType
     * @param string $selected
     * @return void
     */
    public function updateSelected(int $entityId, string $entityType, string $selected): void
    {
        $this->selectedMeme = $selected;
        $memesData = $this->memeDataHandler->getMemes($entityId, $entityType);
        $this->memeDataHandler->saveMemes($entityId, $entityType, $memesData['items'], $selected);
    }

    /**
     * @param int $entityId
     * @param string $entityType
     * @param string $action
     * @return void
     */
    public function updateData(int $entityId, string $entityType, string $action = ''): void
    {
        try {
            $memesUrlArray = $this->giphyApi->getImagesUrl($entityId, $action);
            $this->memeDataHandler->saveMemes($entityId, $entityType, $memesUrlArray, $this->selectedMeme );
        } catch (\Exception $e) {
            $this->logger->critical($e->getMessage());
        }
    }
}
