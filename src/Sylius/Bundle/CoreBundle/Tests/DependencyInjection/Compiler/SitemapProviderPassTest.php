<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Tests\DependencyInjection\Compiler;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractCompilerPassTestCase;
use Matthias\SymfonyDependencyInjectionTest\PhpUnit\DefinitionHasMethodCallConstraint;
use Sylius\Bundle\CoreBundle\DependencyInjection\Compiler\SitemapProviderPass;
use Sylius\Bundle\CoreBundle\Sitemap\Builder\SitemapBuilder;
use Sylius\Bundle\CoreBundle\Sitemap\Provider\ProductUrlProvider;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
class SitemapProviderPassTest extends AbstractCompilerPassTestCase
{
    /**
     * @test
     */
    public function it_adds_method_call_to_sitemap_builder_if_providers_exist()
    {
        $sitemapBuilderDefinition = new Definition(SitemapBuilder::class);
        $this->setDefinition('sylius.sitemap_builder', $sitemapBuilderDefinition);

        $productUrlProviderDefinition = new Definition(ProductUrlProvider::class);
        $productUrlProviderDefinition->addTag('sylius.sitemap_provider');
        $this->setDefinition('sylius.sitemap_provider.product', $productUrlProviderDefinition);

        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithMethodCall(
            'sylius.sitemap_builder',
            'addProvider',
            [
                new Reference('sylius.sitemap_provider.product'),
            ]
        );
    }

    /**
     * @test
     */
    public function it_does_not_add_method_call_if_there_is_no_url_providers()
    {
        $sitemapBuilderDefinition = new Definition(SitemapBuilder::class);
        $this->setDefinition('sylius.sitemap_builder', $sitemapBuilderDefinition);

        $this->compile();

        $this->assertContainerBuilderDoesNotHaveServiceDefinitionWithMethodCall(
            'sylius.sitemap_builder',
            'addProvider'
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function registerCompilerPass(ContainerBuilder $container)
    {
        $container->addCompilerPass(new SitemapProviderPass());
    }

    /**
     * @param string $serviceId
     * @param string $method
     */
    private function assertContainerBuilderDoesNotHaveServiceDefinitionWithMethodCall($serviceId, $method)
    {
        $definition = $this->container->findDefinition($serviceId);

        self::assertThat(
            $definition,
            new \PHPUnit_Framework_Constraint_Not(new DefinitionHasMethodCallConstraint($method))
        );
    }
}
