<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Core\Factory;

use Sylius\Component\Promotion\Model\RuleInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
interface RuleFactoryInterface extends FactoryInterface
{
    /**
     * @param int $count
     *
     * @return RuleInterface
     */
    public function createCartQuantity($count);

    /**
     * @param int $amount
     *
     * @return RuleInterface
     */
    public function createItemTotal($amount);
}
