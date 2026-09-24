<?php
declare(strict_types=1);

namespace Perspective\Voting\Service;

use Magento\Framework\App\Config\ScopeConfigInterface;

/**
 * Service ConfigData Class.
 */
class ConfigData
{
    protected ?bool $isModuleEnabled = null;

    /**
     * Paths to Configurations.
     */
    private const string XML_PATH_ENABLED = 'perspective_voting/general_settings/enabled';
    private const string XML_PATH_ADMIN_EDIT_VOTES = 'perspective_voting/general_settings/allow_admin_edit_votes';
    private const string XML_PATH_SHOW_DISCOUNT_LABEL = 'perspective_voting/general_settings/product_discount_label';
    private const string XML_PATH_SHOW_DISCOUNT_DURATION = 'perspective_voting/general_settings/product_discount_duration';
    private const string XML_PATH_DISCOUNT_LABEL_TEMPLATE = 'perspective_voting/general_settings/product_discount_label_template';
    private const string XML_PATH_GUEST_COOKIE_LIFETIME = 'perspective_voting/general_settings/guest_cookie_ttl';
    private const string XML_PATH_SHOW_VOTING_LINK_IN_MENU = 'perspective_voting/general_settings/display_voting_link_in_top_menu';

    /**
     * @var ScopeConfigInterface
     */
    protected ScopeConfigInterface $scopeConfig;

    /**
     * ConfigData constructor.
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
        if ($this->isModuleEnabled === null) {
            $this->isModuleEnabled = (bool)$this->scopeConfig->getValue(self::XML_PATH_ENABLED);
        }
        return $this->isModuleEnabled;
    }

    /**
     * @return bool
     */
    public function isAdminAllowedEditVotes(): bool
    {
        return $this->scopeConfig->isSetFlag($this::XML_PATH_ADMIN_EDIT_VOTES);
    }

    /**
     * @return bool
     */
    public function isShowDiscountLabel(): bool
    {
        return $this->scopeConfig->isSetFlag($this::XML_PATH_SHOW_DISCOUNT_LABEL);
    }

    /**
     * @return int
     */
    public function getDiscountDuration(): int
    {
        return (int)$this->scopeConfig->getValue($this::XML_PATH_SHOW_DISCOUNT_DURATION);
    }

    /**
     * @return int
     */
    public function getGuestCookieLifetime(): int
    {
        return (int)$this->scopeConfig->getValue($this::XML_PATH_GUEST_COOKIE_LIFETIME) * 86400;
    }

    /**
     * @return string
     */
    public function getDiscountLabelTemplate(): string
    {
        return $this->scopeConfig->getValue($this::XML_PATH_DISCOUNT_LABEL_TEMPLATE);
    }

    /**
     * @return bool
     */
    public function isShowVotingLinkInMenu(): bool
    {
        return $this->scopeConfig->isSetFlag($this::XML_PATH_SHOW_VOTING_LINK_IN_MENU);
    }
}
