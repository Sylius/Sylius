<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\CurrencyBundle\Tests\DependencyInjection;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;
use Sylius\Bundle\CurrencyBundle\DependencyInjection\SyliusCurrencyExtension;
use Sylius\Component\Currency\Attribute\AsCurrencyContext;
use Sylius\Component\Currency\Context\CurrencyContextInterface;
use Symfony\Component\DependencyInjection\Definition;

final class SyliusCurrencyExtensionTest extends AbstractExtensionTestCase
{
    /**
     * @test
     */
    public function it_autoconfigures_currency_contexts(): void
    {
        $this->container->setDefinition(
            'acme.currency_context_autoconfigured',
            (new Definition())
                ->setClass(self::getMockClass(CurrencyContextInterface::class))
                ->setAutoconfigured(true)
        );

        $this->load();
        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithTag(
            'acme.currency_context_autoconfigured',
            'sylius.context.currency'
        );
    }

    /** @test */
    public function it_autoconfigures_currency_context_with_attribute(): void
    {
        $this->container->register(
            'acme.currency_context_autoconfigured',
            DummyCurrencyContext::class
        )->setAutoconfigured(true);

        $this->container->register(
            'acme.prioritized_currency_context_autoconfigured',
            PrioritizedDummyCurrencyContext::class
        )->setAutoconfigured(true);

        $this->load();
        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithTag(
            'acme.currency_context_autoconfigured',
            'sylius.context.currency'
        );

        $this->assertContainerBuilderHasServiceDefinitionWithTag(
            'acme.prioritized_currency_context_autoconfigured',
            'sylius.context.currency',
            ['priority' => 256]
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function getContainerExtensions(): array
    {
        return [new SyliusCurrencyExtension()];
    }
}

#[AsCurrencyContext]
class DummyCurrencyContext implements CurrencyContextInterface
{
    public function getCurrencyCode(): string
    {
        return 'EUR';
    }
}

#[AsCurrencyContext(priority: 256)]
class PrioritizedDummyCurrencyContext implements CurrencyContextInterface
{
    public function getCurrencyCode(): string
    {
        return 'EUR';
    }
}
