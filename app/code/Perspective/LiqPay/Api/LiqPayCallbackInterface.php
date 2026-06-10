<?php
/**
 * LiqPay Extension for Magento 2.
 *
 * @author PerspectiveTeam<order@perspectiveteam.com>
 * © Perspective. All rights reserved
 */
declare(strict_types=1);

namespace Perspective\LiqPay\Api;

/**
 * LiqPayCallbackInterface Interface.
 */
interface LiqPayCallbackInterface
{
    /**
     * @return mixed
     */
    public function callback(): mixed;
}
