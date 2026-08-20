<?php
declare(strict_types=1);

namespace Perspective\Memes\Service;

use Magento\Framework\App\Config\ScopeConfigInterface;

/**
 * Service Config Class.
 */
class Config
{
    /**
     * Paths to Configurations.
     */
    private const string XML_PATH_ENABLED = 'ps_giphy_memes/general_settings/enabled';
    private const string XML_PATH_GIF_COUNT = 'ps_giphy_memes/general_settings/gifs_count';
    private const string XML_PATH_API_URL = 'ps_giphy_memes/general_settings/api_url';
    private const string XML_PATH_API_KEY = 'ps_giphy_memes/general_settings/api_key';

    /**
     * @var ScopeConfigInterface
     */
    protected ScopeConfigInterface $scopeConfig;

    /**
     * Config constructor.
     *
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig
    ) {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * @return bool
     */
    public function isModuleEnabled(): bool
    {
        return $this->scopeConfig->isSetFlag($this::XML_PATH_ENABLED);
    }

    /**
     * @return int
     */
    public function getGifsCount(): int
    {
        return (int)$this->scopeConfig->getValue($this::XML_PATH_GIF_COUNT);
    }

    /**
     * @return string
     */
    public function getGiphyApiUrl(): string
    {
        return $this->scopeConfig->getValue($this::XML_PATH_API_URL);
    }

    /**
     * @return string
     */
    public function getGiphyApiKey(): string
    {
        return $this->scopeConfig->getValue($this::XML_PATH_API_KEY);
    }
}
