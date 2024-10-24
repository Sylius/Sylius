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
use Sylius\Component\Order\Processor\OrderProcessorInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class RegisterProcessorsPass extends PrioritizedCompositeServicePass
{
    public const PROCESSOR_SERVICE_TAG = 'sylius.order_processor';

    public function __construct()
    {
        parent::__construct(
            'sylius.order_processing.order_processor',
            'sylius.order_processing.order_processor.composite',
            self::PROCESSOR_SERVICE_TAG,
            'addProcessor',
        );
    }

    public function process(ContainerBuilder $container): void
    {
        parent::process($container);

        $container->setAlias(OrderProcessorInterface::class, 'sylius.order_processing.order_processor');
    }
}
