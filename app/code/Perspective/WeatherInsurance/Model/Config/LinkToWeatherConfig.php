<?php
/**
 * Viktoriia Sira <viktoriia.s@perspectiveteam.com>
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Perspective\WeatherInsurance\Model\Config;

use Magento\Config\Model\Config\CommentInterface;
use Magento\Framework\Phrase;
use Magento\Framework\UrlInterface;

/**
 * LinkToWeatherConfig Class.
 */
class LinkToWeatherConfig implements CommentInterface
{
    /**
     * @var UrlInterface
     */
    protected UrlInterface $urlInterface;

    /**
     * LinkToWeatherConfig constructor.
     *
     * @param UrlInterface $urlInterface
     */
    public function __construct(
        UrlInterface $urlInterface
    ) {
        $this->urlInterface = $urlInterface;
    }

    /**
     * @param $elementValue
     * @return Phrase|string
     */
    public function getCommentText($elementValue): Phrase|string
    {
        $targetSectionId = 'weather_widget';

        $url = $this->urlInterface->getUrl(
            'adminhtml/system_config/edit',
            ['section' => $targetSectionId]
        );

        return __(
            'You can configure your Weather Settings <a href="%1" target="_blank">here</a>',
            $url
        );
    }
}
