<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\DependencyInjection\Compiler;

use Sylius\Bundle\ResourceBundle\DependencyInjection\Compiler\PrioritizedCompositeServicePass;

/**
 * @author Jan Góralski <jan.goralski@lakion.com>
 */
final class RegisterCurrencyHandlersPass extends PrioritizedCompositeServicePass
{
    public function __construct()
    {
        parent::__construct(
            'sylius.handler.currency_change',
            'sylius.handler.currency_change.composite',
            'sylius.currency.change_handler',
            'addHandler'
        );
    }
}
