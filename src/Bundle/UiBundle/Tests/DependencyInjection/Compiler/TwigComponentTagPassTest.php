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
use Sylius\Bundle\UiBundle\DependencyInjection\Compiler\TwigComponentTagPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

final class TwigComponentTagPassTest extends AbstractCompilerPassTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->container->setParameter('sylius_ui.twig_ux.component_default_template', '@SyliusUi/components/default.html.twig');
    }

    public function testAddingTwigComponentTagToServicesTaggedWithTwigComponentTag(): void
    {
        $twigComponent = new Definition();
        $twigComponent->addTag('sylius.twig_component', ['key' => 'foo', 'template' => 'bar']);

        $this->setDefinition('my_twig_component', $twigComponent);

        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithTag(
            'my_twig_component',
            'twig.component',
            [
                'key' => 'foo',
                'template' => 'bar',
                'expose_public_props' => true,
                'attributes_var' => 'attributes',
            ],
        );
    }

    public function testOverridingTagAttributesWithTwigComponentTag(): void
    {
        $twigComponent = new Definition();
        $twigComponent->addTag('sylius.twig_component', [
            'key' => 'foo',
            'template' => 'bar',
            'expose_public_props' => false,
            'attributes_var' => 'custom_attributes',
        ]);

        $this->setDefinition('my_twig_component', $twigComponent);

        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithTag(
            'my_twig_component',
            'twig.component',
            [
                'key' => 'foo',
                'template' => 'bar',
                'expose_public_props' => false,
                'attributes_var' => 'custom_attributes',
            ],
        );
    }

    public function testUsingDefaultTemplateWithTwigComponentTag(): void
    {
        $twigComponent = new Definition();
        $twigComponent->addTag('sylius.twig_component', [
            'key' => 'foo',
            'expose_public_props' => false,
        ]);

        $this->setDefinition('my_twig_component', $twigComponent);

        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithTag(
            'my_twig_component',
            'twig.component',
            [
                'key' => 'foo',
                'template' => '@SyliusUi/components/default.html.twig',
                'expose_public_props' => false,
                'attributes_var' => 'attributes',
            ],
        );
    }

    public function testThrowingExceptionWhenKeyIsNotPresentOnTwigComponentTag(): void
    {
        $twigComponent = new Definition();
        $twigComponent->addTag('sylius.twig_component', ['template' => 'bar']);

        $this->setDefinition('my_twig_component', $twigComponent);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('The "key" attribute is required for the "sylius.twig_component" tag');

        $this->compile();
    }

    protected function registerCompilerPass(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new TwigComponentTagPass());
    }
}
