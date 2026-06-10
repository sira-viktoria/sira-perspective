<?php
/**
 * LiqPay Extension for Magento 2.
 *
 * @author PerspectiveTeam<order@perspectiveteam.com>
 * © Perspective. All rights reserved
 */
declare(strict_types=1);

use \Magento\Framework\Component\ComponentRegistrar;

ComponentRegistrar::register(
    ComponentRegistrar::MODULE,
    'Perspective_LiqPay',
    __DIR__
);
