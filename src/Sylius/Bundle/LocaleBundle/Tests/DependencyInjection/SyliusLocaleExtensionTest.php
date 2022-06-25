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

namespace Sylius\Bundle\LocaleBundle\Tests\DependencyInjection;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;
use Sylius\Bundle\LocaleBundle\DependencyInjection\SyliusLocaleExtension;
use Sylius\Component\Locale\Attribute\AsLocaleContext;
use Sylius\Component\Locale\Context\LocaleContextInterface;
use Symfony\Component\DependencyInjection\Definition;

final class SyliusLocaleExtensionTest extends AbstractExtensionTestCase
{
    /**
     * @test
     */
    public function it_autoconfigures_locale_contexts(): void
    {
        $this->container->setDefinition(
            'acme.locale_context_autoconfigured',
            (new Definition())
                ->setClass(self::getMockClass(LocaleContextInterface::class))
                ->setAutoconfigured(true)
        );

        $this->load();
        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithTag(
            'acme.locale_context_autoconfigured',
            'sylius.context.locale'
        );
    }

    /** @test */
    public function it_autoconfigures_locale_contexts_with_attribute(): void
    {
        $this->container->register(
            'acme.locale_context_autoconfigured',
            DummyLocaleContext::class
        )->setAutoconfigured(true);

        $this->container->register(
            'acme.prioritized_locale_context_autoconfigured',
            PrioritizedDummyLocaleContext::class
        )->setAutoconfigured(true);

        $this->load();
        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithTag(
            'acme.locale_context_autoconfigured',
            'sylius.context.locale'
        );

        $this->assertContainerBuilderHasServiceDefinitionWithTag(
            'acme.prioritized_locale_context_autoconfigured',
            'sylius.context.locale',
            ['priority' => 256]
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function getContainerExtensions(): array
    {
        return [new SyliusLocaleExtension()];
    }
}

#[AsLocaleContext]
class DummyLocaleContext implements LocaleContextInterface
{
    public function getLocaleCode(): string
    {
        return 'fr_FR';
    }
}

#[AsLocaleContext(priority: 256)]
class PrioritizedDummyLocaleContext implements LocaleContextInterface
{
    public function getLocaleCode(): string
    {
        return 'fr_FR';
    }
}
