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

namespace DependencyInjection\Compiler;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractCompilerPassTestCase;
use Sylius\Bundle\UiBundle\DependencyInjection\Compiler\LiveComponentTagPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final class LiveComponentTagPassTest extends AbstractCompilerPassTestCase
{
    public function testAddingTwigComponentTagToServicesTaggedWithLiveComponentTag(): void
    {
        $liveComponent = new Definition();
        $liveComponent->addTag('sylius.live_component', ['key' => 'foo', 'template' => 'bar']);

        $this->setDefinition('my_live_component', $liveComponent);

        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithTag(
            'my_live_component',
            'twig.component',
            [
                'key' => 'foo',
                'template' => 'bar',
                'expose_public_props' => true,
                'attributes_var' => 'attributes',
                'default_action' => null,
                'live' => true,
                'csrf' => true,
                'route' => 'ux_live_component',
                'method' => 'post',
                'url_reference_type' => UrlGeneratorInterface::ABSOLUTE_PATH,
            ],
        );
        $this->assertContainerBuilderHasServiceDefinitionWithTag('my_live_component', 'controller.service_arguments');
    }

    public function testOverridingTagAttributesWithLiveComponentTag(): void
    {
        $liveComponent = new Definition();
        $liveComponent->addTag('sylius.live_component', [
            'key' => 'foo',
            'template' => 'bar',
            'expose_public_props' => false,
            'attributes_var' => 'custom_attributes',
            'default_action' => 'customAction',
            'csrf' => false,
            'route' => 'custom_route',
            'method' => 'get',
            'url_reference_type' => UrlGeneratorInterface::ABSOLUTE_URL,
        ]);

        $this->setDefinition('my_live_component', $liveComponent);

        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithTag(
            'my_live_component',
            'twig.component',
            [
                'key' => 'foo',
                'template' => 'bar',
                'expose_public_props' => false,
                'attributes_var' => 'custom_attributes',
                'default_action' => 'customAction',
                'live' => true,
                'csrf' => false,
                'route' => 'custom_route',
                'method' => 'get',
                'url_reference_type' => UrlGeneratorInterface::ABSOLUTE_URL,
            ],
        );
    }

    public function testThrowingExceptionWhenKeyIsNotPresentOnLiveComponentTag(): void
    {
        $liveComponent = new Definition();
        $liveComponent->addTag('sylius.live_component', ['template' => 'bar']);

        $this->setDefinition('my_live_component', $liveComponent);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('The "key" attribute is required for the "sylius.live_component" tag');

        $this->compile();
    }

    public function testThrowingExceptionWhenTemplateIsNotPresentOnLiveComponentTag(): void
    {
        $liveComponent = new Definition();
        $liveComponent->addTag('sylius.live_component', ['key' => 'foo']);

        $this->setDefinition('my_live_component', $liveComponent);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('The "template" attribute is required for the "sylius.live_component" tag');

        $this->compile();
    }

    protected function registerCompilerPass(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new LiveComponentTagPass());
    }
}
