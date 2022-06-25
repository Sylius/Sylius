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

namespace Sylius\Bundle\ChannelBundle\Tests\DependencyInjection;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;
use Sylius\Bundle\ChannelBundle\DependencyInjection\SyliusChannelExtension;
use Sylius\Component\Channel\Attribute\AsChannelContext;
use Sylius\Component\Channel\Attribute\AsChannelContextRequestResolver;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Channel\Context\RequestBased\RequestResolverInterface;
use Sylius\Component\Channel\Model\ChannelInterface;
use Sylius\Component\Core\Model\Channel;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\HttpFoundation\Request;

final class SyliusChannelExtensionTest extends AbstractExtensionTestCase
{
    public function setUp(): void
    {
        parent::setUp();

        $this->container->setParameter('kernel.debug', false);
    }

    /** @test */
    public function it_fallbacks_to_enabled_kernel_debug_parameter_if_debug_is_not_defined(): void
    {
        $this->container->setParameter('kernel.debug', true);

        $this->load([]);

        $this->assertContainerBuilderHasServiceDefinitionWithArgument('sylius.channel_collector', 2, true);
    }

    /** @test */
    public function it_fallbacks_to_disabled_kernel_debug_parameter_if_debug_is_not_defined(): void
    {
        $this->load([]);

        $this->assertContainerBuilderHasServiceDefinitionWithArgument('sylius.channel_collector', 2, false);
    }

    /** @test */
    public function it_uses_enabled_debug_config_if_defined(): void
    {
        $this->load(['debug' => true]);

        $this->assertContainerBuilderHasServiceDefinitionWithArgument('sylius.channel_collector', 2, true);
    }

    /** @test */
    public function it_uses_disabled_debug_config_if_defined(): void
    {
        $this->load(['debug' => false]);

        $this->assertContainerBuilderHasServiceDefinitionWithArgument('sylius.channel_collector', 2, false);
    }

    protected function getContainerExtensions(): array
    {
        return [
            new SyliusChannelExtension(),
        ];
    }

    /** @test */
    public function it_autoconfigures_channel_contexts_and_request_resolvers(): void
    {
        $this->container->setDefinition(
            'acme.channel_context_autoconfigured',
            (new Definition())
                ->setClass(self::getMockClass(ChannelContextInterface::class))
                ->setAutoconfigured(true)
        );

        $this->container->setDefinition(
            'acme.request_resolver_autoconfigured',
            (new Definition())
                ->setClass(self::getMockClass(RequestResolverInterface::class))
                ->setAutoconfigured(true)
        );

        $this->load();
        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithTag(
            'acme.channel_context_autoconfigured',
            'sylius.context.channel'
        );

        $this->assertContainerBuilderHasServiceDefinitionWithTag(
            'acme.request_resolver_autoconfigured',
            'sylius.context.channel.request_based.resolver'
        );
    }

    /** @test */
    public function it_autoconfigures_channel_contexts_with_attribute(): void
    {
        $this->container->register(
            'acme.channel_context_autoconfigured',
            DummyChannelContext::class
        )->setAutoconfigured(true);

        $this->container->register(
            'acme.prioritized_channel_context_autoconfigured',
            PrioritizedDummyChannelContext::class
        )->setAutoconfigured(true);

        $this->load();
        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithTag(
            'acme.channel_context_autoconfigured',
            'sylius.context.channel'
        );

        $this->assertContainerBuilderHasServiceDefinitionWithTag(
            'acme.prioritized_channel_context_autoconfigured',
            'sylius.context.channel',
            ['priority' => 256]
        );
    }

    /** @test */
    public function it_autoconfigures_channel_contexts_request_resolver_with_attribute(): void
    {
        $this->container->register(
            'acme.channel_context_request_resolver_autoconfigured',
            DummyChannelContextRequestResolver::class
        )->setAutoconfigured(true);

        $this->container->register(
            'acme.prioritized_channel_context_request_resolver_autoconfigured',
            PrioritizedDummyChannelContextRequestResolver::class
        )->setAutoconfigured(true);

        $this->load();
        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithTag(
            'acme.channel_context_request_resolver_autoconfigured',
            'sylius.context.channel.request_based.resolver'
        );

        $this->assertContainerBuilderHasServiceDefinitionWithTag(
            'acme.prioritized_channel_context_request_resolver_autoconfigured',
            'sylius.context.channel.request_based.resolver',
            ['priority' => 256]
        );
    }
}

#[AsChannelContext]
class DummyChannelContext implements ChannelContextInterface
{
    public function getChannel(): ChannelInterface
    {
        return new Channel();
    }
}

#[AsChannelContext(priority: 256)]
class PrioritizedDummyChannelContext implements ChannelContextInterface
{
    public function getChannel(): ChannelInterface
    {
        return new Channel();
    }
}

#[AsChannelContextRequestResolver]
class DummyChannelContextRequestResolver implements RequestResolverInterface
{
    public function findChannel(Request $request): ?ChannelInterface
    {
        return new Channel();
    }
}

#[AsChannelContextRequestResolver(priority: 256)]
class PrioritizedDummyChannelContextRequestResolver implements RequestResolverInterface
{
    public function findChannel(Request $request): ?ChannelInterface
    {
        return new Channel();
    }
}
