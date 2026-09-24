<?php
declare(strict_types=1);

namespace Perspective\Voting\Service\Guest;

use Magento\Framework\Exception\InputException;
use Magento\Framework\Stdlib\Cookie\CookieSizeLimitReachedException;
use Magento\Framework\Stdlib\Cookie\FailureToSendException;
use Magento\Framework\Stdlib\CookieManagerInterface;
use Magento\Framework\Stdlib\Cookie\CookieMetadataFactory;
use Perspective\Voting\Service\ConfigData;

/**
 * CookieManager Class.
 */
class CookieManager
{
    public const string GUEST_COOKIE_NAME = 'voting_guest_hash';

    /**
     * @var CookieManagerInterface
     */
    protected CookieManagerInterface $cookieManager;

    /**
     * @var CookieMetadataFactory
     */
    protected CookieMetadataFactory $cookieMetadataFactory;

    /**
     * @var ConfigData
     */
    protected ConfigData $configDataService;

    /**
     * CookieManager constructor.
     *
     * @param CookieManagerInterface $cookieManager
     * @param CookieMetadataFactory $cookieMetadataFactory
     * @param ConfigData $configDataService
     */
    public function __construct(
        CookieManagerInterface $cookieManager,
        CookieMetadataFactory $cookieMetadataFactory,
        ConfigData $configDataService,
    ) {
        $this->cookieManager = $cookieManager;
        $this->cookieMetadataFactory = $cookieMetadataFactory;
        $this->configDataService = $configDataService;
    }

    /**
     * @return string|null
     */
    public function getGuestCookie(): ?string
    {
        return $this->cookieManager->getCookie(self::GUEST_COOKIE_NAME);
    }

    /**
     * @param $cookieData
     *
     * @return void
     * @throws InputException
     * @throws CookieSizeLimitReachedException
     * @throws FailureToSendException
     */
    public function setGuestCookie($cookieData): void
    {
        $metadata = $this->cookieMetadataFactory
            ->createPublicCookieMetadata()
            ->setDuration($this->configDataService->getGuestCookieLifetime())
            ->setPath('/')
            ->setHttpOnly(true);

        $this->cookieManager->setPublicCookie(self::GUEST_COOKIE_NAME, $cookieData, $metadata);
    }

    /**
     * @return void
     * @throws InputException
     * @throws FailureToSendException
     */
    public function deleteGuestCookie(): void
    {
        $this->cookieManager->deleteCookie(self::GUEST_COOKIE_NAME);
    }
}
