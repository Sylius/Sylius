<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
 
namespace spec\Sylius\Bundle\CoreBundle\DependencyInjection\Compiler;
 
use PhpSpec\ObjectBehavior;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
class SitemapProviderCompilerPassSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\CoreBundle\DependencyInjection\Compiler\SitemapProviderCompilerPass');
    }

    function it_implements_compiler_pass_interface()
    {
        $this->shouldImplement('Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface');
    }

    function it_finds_tagged_providers_and_add_it_to_array_if_builder_is_registered(
        ContainerBuilder $container,
        Definition $sitemapBuilderDefinition
    ) {
        $container->has('sylius.sitemap.service.builder')->willReturn(true);

        $providerServices = array(
            'sylius.sitemap.provider.product' => array(
                array()
            ),
        );

        $container->findDefinition('sylius.sitemap.service.builder')->willReturn($sitemapBuilderDefinition);
        $container->findTaggedServiceIds('sylius.sitemap.provider')->willReturn($providerServices);
        $sitemapBuilderDefinition->addMethodCall('addProvider', array(new Reference('sylius.sitemap.provider.product')))->shouldBeCalled();

        $this->process($container);
    }
}
