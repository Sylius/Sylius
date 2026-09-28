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

namespace Sylius\Bundle\PayumBundle\Tests\DependencyInjection;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

final class PaymentRequestContextServiceTest extends TestCase
{
    #[Test]
    public function it_registers_payment_request_context_to_be_reset_between_requests(): void
    {
        $container = new ContainerBuilder();
        $loader = new XmlFileLoader(
            $container,
            new FileLocator(__DIR__ . '/../../Resources/config/services/payment_request'),
        );
        $loader->load('context.xml');

        $definition = $container->getDefinition('sylius_payum.context.payment_request');

        self::assertSame(
            [['method' => 'reset']],
            $definition->getTag('kernel.reset'),
            'The payment request context must be tagged with "kernel.reset" so it is reset between worker requests.',
        );
    }
}
