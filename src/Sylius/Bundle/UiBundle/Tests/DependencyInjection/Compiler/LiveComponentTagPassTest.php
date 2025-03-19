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
    protected function setUp(): void
    {
        parent::setUp();
        $this->container->setParameter('sylius_ui.twig_ux.live_component_tags', []);
        $this->container->setParameter('sylius_ui.twig_ux.component_default_template', '@SyliusUi/components/default.html.twig');
    }

    public function testAddingTwigComponentTagToServicesTaggedWithLiveComponentTag(): void
    {
        $liveComponent = new Definition();
        $liveComponent->addTag('sylius.live_component.ui', ['key' => 'foo', 'template' => 'bar']);

        $this->setParameter('sylius_ui.twig_ux.live_component_tags', ['ui' => ['route' => 'sylius_ui_live_component']]);
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
                'route' => 'sylius_ui_live_component',
                'method' => 'post',
                'url_reference_type' => UrlGeneratorInterface::ABSOLUTE_PATH,
            ],
        );
        $this->assertContainerBuilderHasServiceDefinitionWithTag('my_live_component', 'controller.service_arguments');
    }

    public function testOverridingTagAttributesWithLiveComponentTag(): void
    {
        $liveComponent = new Definition();
        $liveComponent->addTag('sylius.live_component.ui', [
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

        $this->setParameter('sylius_ui.twig_ux.live_component_tags', ['ui' => ['route' => 'sylius_ui_live_component']]);
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

    public function testOverridingTagAttributesWithDefaultTagOptions(): void
    {
        $liveComponent = new Definition();
        $liveComponent->addTag('sylius.live_component.ui', ['key' => 'foo', 'template' => 'bar']);

        $this->setParameter(
            'sylius_ui.twig_ux.live_component_tags',
            [
                'ui' => [
                        'expose_public_props' => false,
                        'attributes_var' => 'custom_attributes',
                        'default_action' => 'customAction',
                        'csrf' => false,
                        'route' => 'sylius_ui_live_component',
                        'method' => 'get',
                        'url_reference_type' => UrlGeneratorInterface::ABSOLUTE_URL,
                    ],
                ],
        );
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
                'route' => 'sylius_ui_live_component',
                'method' => 'get',
                'url_reference_type' => UrlGeneratorInterface::ABSOLUTE_URL,
            ],
        );
        $this->assertContainerBuilderHasServiceDefinitionWithTag('my_live_component', 'controller.service_arguments');
    }

    public function testThrowingExceptionWhenKeyIsNotPresentOnLiveComponentTag(): void
    {
        $liveComponent = new Definition();
        $liveComponent->addTag('sylius.live_component.ui', ['template' => 'bar']);

        $this->setParameter('sylius_ui.twig_ux.live_component_tags', ['ui' => ['route' => 'sylius_ui_live_component']]);
        $this->setDefinition('my_live_component', $liveComponent);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('The "key" attribute is required for the "sylius.live_component" tag');

        $this->compile();
    }

    protected function registerCompilerPass(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new LiveComponentTagPass());
    }
}
