<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\LocaleBundle\DependencyInjection\Compiler;

use Sylius\Bundle\ResourceBundle\DependencyInjection\Compiler\PrioritizedCompositeServicePass;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
final class CompositeLocaleContextPass extends PrioritizedCompositeServicePass
{
    public function __construct()
    {
        parent::__construct(
            'sylius.context.locale',
            'sylius.context.locale.composite',
            'sylius.context.locale',
            'addContext'
        );
    }
}
