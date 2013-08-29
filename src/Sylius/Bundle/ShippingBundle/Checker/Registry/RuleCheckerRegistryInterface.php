<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ShippingBundle\Checker\Registry;

use Sylius\Bundle\ShippingBundle\Checker\RuleCheckerInterface;

/**
 * Rule checker registry interface.
 *
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
interface RuleCheckerRegistryInterface
{
    public function getCheckers();
    public function registerChecker($name, RuleCheckerInterface $checker);
    public function unregisterChecker($name);
    public function hasChecker($name);
    public function getChecker($name);
}
