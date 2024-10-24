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

namespace Sylius\Bundle\CurrencyBundle\DependencyInjection\Compiler;

use Sylius\Bundle\ResourceBundle\DependencyInjection\Compiler\PrioritizedCompositeServicePass;
use Sylius\Component\Currency\Context\CurrencyContextInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class CompositeCurrencyContextPass extends PrioritizedCompositeServicePass
{
    public function __construct()
    {
        parent::__construct(
            'sylius.context.currency',
            'sylius.context.currency.composite',
            'sylius.context.currency',
            'addContext',
        );
    }

    public function process(ContainerBuilder $container): void
    {
        parent::process($container);

        $container->setAlias(CurrencyContextInterface::class, 'sylius.context.currency');
    }
}
