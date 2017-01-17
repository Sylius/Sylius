<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\OrderBundle\DependencyInjection\Compiler;

use Sylius\Bundle\ResourceBundle\DependencyInjection\Compiler\PrioritizedCompositeServicePass;

/**
 * @author Gorka Laucirica <gorka.lauzirika@gmail.com>
 */
final class RegisterProcessorsPass extends PrioritizedCompositeServicePass
{
    public function __construct()
    {
        parent::__construct(
            'sylius.order_processing.order_processor',
            'sylius.order_processing.order_processor.composite',
            'sylius.order_processor',
            'addProcessor'
        );
    }
}
