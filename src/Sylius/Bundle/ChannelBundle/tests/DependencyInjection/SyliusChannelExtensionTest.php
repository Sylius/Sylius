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

namespace Tests\Sylius\Bundle\ChannelBundle\DependencyInjection;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;
use PHPUnit\Framework\Attributes\Test;
use Sylius\Bundle\ChannelBundle\Attribute\AsChannelContext;
use Sylius\Bundle\ChannelBundle\Attribute\AsRequestBasedChannelResolver;
use Sylius\Bundle\ChannelBundle\DependencyInjection\SyliusChannelExtension;
use Symfony\Component\DependencyInjection\Definition;
use Tests\Sylius\Bundle\ChannelBundle\Stub\ChannelContextStub;
use Tests\Sylius\Bundle\ChannelBundle\Stub\RequestBestChannelResolverStub;

final class SyliusChannelExtensionTest extends AbstractExtensionTestCase
{
    #[Test]
    public function it_fallbacks_to_enabled_kernel_debug_parameter_if_debug_is_not_defined(): void
    {
        $this->container->setParameter('kernel.debug', true);

        $this->load([]);

        $this->assertContainerBuilderHasServiceDefinitionWithArgument('sylius.collector.channel', 2, true);
    }

    #[Test]
    public function it_fallbacks_to_disabled_kernel_debug_parameter_if_debug_is_not_defined(): void
    {
        $this->container->setParameter('kernel.debug', false);

        $this->load([]);

        $this->assertContainerBuilderHasServiceDefinitionWithArgument('sylius.collector.channel', 2, false);
    }

    #[Test]
    public function it_uses_enabled_debug_config_if_defined(): void
    {
        $this->load(['debug' => true]);

        $this->assertContainerBuilderHasServiceDefinitionWithArgument('sylius.collector.channel', 2, true);
    }

    #[Test]
    public function it_uses_disabled_debug_config_if_defined(): void
    {
        $this->load(['debug' => false]);

        $this->assertContainerBuilderHasServiceDefinitionWithArgument('sylius.collector.channel', 2, false);
    }

    #[Test]
    public function it_autoconfigures_channel_context_with_attribute(): void
    {
        $this->container->setDefinition(
            'acme.channel_context_with_attribute',
            (new Definition())
                ->setClass(ChannelContextStub::class)
                ->setAutoconfigured(true),
        );

        $this->load(['debug' => false]);
        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithTag(
            'acme.channel_context_with_attribute',
            AsChannelContext::SERVICE_TAG,
            ['priority' => 15],
        );
    }

    #[Test]
    public function it_autoconfigures_request_based_channel_resolver_with_attribute(): void
    {
        $this->container->setDefinition(
            'acme.channel_context_request_resolver_with_attribute',
            (new Definition())
                ->setClass(RequestBestChannelResolverStub::class)
                ->setAutoconfigured(true),
        );

        $this->load(['debug' => false]);
        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithTag(
            'acme.channel_context_request_resolver_with_attribute',
            AsRequestBasedChannelResolver::SERVICE_TAG,
            ['priority' => 20],
        );
    }

    protected function getContainerExtensions(): array
    {
        return [
            new SyliusChannelExtension(),
        ];
    }
}
