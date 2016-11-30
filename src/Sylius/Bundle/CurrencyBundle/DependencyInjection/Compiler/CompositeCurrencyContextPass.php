<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CurrencyBundle\DependencyInjection\Compiler;

use Sylius\Bundle\ResourceBundle\DependencyInjection\Compiler\PrioritizedCompositeServicePass;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
final class CompositeCurrencyContextPass extends PrioritizedCompositeServicePass
{
    public function __construct()
    {
        parent::__construct(
            'sylius.context.currency',
            'sylius.context.currency.composite',
            'sylius.context.currency',
            'addContext'
        );
    }
}
