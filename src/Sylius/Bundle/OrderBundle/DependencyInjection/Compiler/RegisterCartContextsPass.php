<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\OrderBundle\DependencyInjection\Compiler;

use Sylius\Bundle\ResourceBundle\DependencyInjection\Compiler\PrioritizedCompositeServicePass;
use Sylius\Component\Order\Context\CartContextInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class RegisterCartContextsPass extends PrioritizedCompositeServicePass
{
    public const CART_CONTEXT_SERVICE_TAG = 'sylius.context.cart';

    public function __construct()
    {
        parent::__construct(
            'sylius.context.cart',
            'sylius.context.cart.composite',
            self::CART_CONTEXT_SERVICE_TAG,
            'addContext',
        );
    }

    public function process(ContainerBuilder $container): void
    {
        parent::process($container);

        $container->setAlias(CartContextInterface::class, 'sylius.context.cart');
    }
}
