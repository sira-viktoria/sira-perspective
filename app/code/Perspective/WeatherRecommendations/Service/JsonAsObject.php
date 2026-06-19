<?php
/**
 * Viktoriia Sira <viktoriia.s@perspectiveteam.com>
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Perspective\WeatherRecommendations\Service;

use Magento\Framework\Serialize\SerializerInterface;

/**
 * Serializer Class.
 */
class JsonAsObject implements SerializerInterface
{
    /**
     * @inheritDoc
     * @since 101.0.0
     */
    public function serialize($data): bool|string
    {
        $result = json_encode($data);
        if (false === $result) {
            throw new \InvalidArgumentException("Unable to serialize value. Error: " . json_last_error_msg());
        }
        return $result;
    }

    /**
     * @inheritDoc
     * @since 101.0.0
     */
    public function unserialize($string, $associative = false)
    {
        if ($string === null) {
            throw new \InvalidArgumentException(
                'Unable to unserialize value. Error: Parameter must be a string type, null given.'
            );
        }
        $result = json_decode($string, $associative);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \InvalidArgumentException("Unable to unserialize value. Error: " . json_last_error_msg());
        }
        return $result;
    }
}
