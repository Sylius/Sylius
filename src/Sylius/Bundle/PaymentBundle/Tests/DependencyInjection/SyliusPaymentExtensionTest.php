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

namespace Sylius\Bundle\PaymentBundle\Tests\DependencyInjection;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;
use Sylius\Bundle\PaymentBundle\Attribute\AsPaymentMethodsResolver;
use Sylius\Bundle\PaymentBundle\DependencyInjection\SyliusPaymentExtension;
use Sylius\Bundle\PaymentBundle\Tests\Stub\PaymentMethodsResolverStub;
use Symfony\Component\DependencyInjection\Definition;

final class SyliusPaymentExtensionTest extends AbstractExtensionTestCase
{
    /** @test */
    public function it_autoconfigures_payment_methods_resolver_with_attribute(): void
    {
        $this->container->setDefinition(
            'acme.payment_methods_resolver_with_attribute',
            (new Definition())
                ->setClass(PaymentMethodsResolverStub::class)
                ->setAutoconfigured(true),
        );

        $this->load();
        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithTag(
            'acme.payment_methods_resolver_with_attribute',
            AsPaymentMethodsResolver::SERVICE_TAG,
            ['type' => 'test', 'label' => 'Test', 'priority' => 15],
        );
    }

    protected function getContainerExtensions(): array
    {
        return [new SyliusPaymentExtension()];
    }
}
