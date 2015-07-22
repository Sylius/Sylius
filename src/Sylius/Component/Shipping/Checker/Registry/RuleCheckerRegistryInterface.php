<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Shipping\Checker\Registry;

use Sylius\Component\Shipping\Checker\RuleCheckerInterface;

/**
 * Rule checker registry interface.
 *
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
interface RuleCheckerRegistryInterface
{
    /**
     * @return RuleCheckerInterface[]
     */
    public function getCheckers();

    /**
     * @param string               $name
     * @param RuleCheckerInterface $checker
     */
    public function registerChecker($name, RuleCheckerInterface $checker);

    /**
     * @param string $name
     */
    public function unregisterChecker($name);

    /**
     * @param string $name
     *
     * @return Boolean
     */
    public function hasChecker($name);

    /**
     * @param string $name
     *
     * @return RuleCheckerInterface
     */
    public function getChecker($name);
}
