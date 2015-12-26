<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\ResourceBundle\Routing;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\ResourceBundle\Routing\ResourceLoader;
use Sylius\Bundle\ResourceBundle\Routing\RouteFactoryInterface;
use Sylius\Component\Resource\Metadata\MetadataInterface;
use Sylius\Component\Resource\Metadata\RegistryInterface;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

/**
 * @mixin ResourceLoader
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class ResourceLoaderSpec extends ObjectBehavior
{
    function let(RegistryInterface $resourceRegistry, RouteFactoryInterface $routeFactory)
    {
        $this->beConstructedWith($resourceRegistry, $routeFactory);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\ResourceBundle\Routing\ResourceLoader');
    }
    
    function it_is_a_Symfony_routing_loader()
    {
        $this->shouldImplement(LoaderInterface::class);
    }

    function it_throws_an_exception_if_invalid_resource_configured(RegistryInterface $resourceRegistry) 
    {
        $resourceRegistry->get('sylius.foo')->willThrow(new \InvalidArgumentException());

        $configuration =
<<<EOT
alias: sylius.foo
EOT;

        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('load', array($configuration, 'sylius.resource'))
        ;
    }

    function it_generates_routing_based_on_resource_configuration(
        RegistryInterface $resourceRegistry,
        MetadataInterface $metadata,
        RouteFactoryInterface $routeFactory,
        RouteCollection $routeCollection,
        Route $showRoute,
        Route $indexRoute,
        Route $createRoute,
        Route $updateRoute,
        Route $deleteRoute
    ) {
        $resourceRegistry->get('sylius.product')->willReturn($metadata);
        $metadata->getApplicationName()->willReturn('sylius');
        $metadata->getName()->willReturn('product');
        $metadata->getPluralName()->willReturn('products');
        $metadata->getServiceId('controller')->willReturn('sylius.controller.product');

        $routeFactory->createRouteCollection()->willReturn($routeCollection);

        $configuration =
<<<EOT
alias: sylius.product
EOT;

        $showDefaults = array(
            '_controller' => 'sylius.controller.product:showAction'
        );
        $routeFactory->createRoute('/products/{id}', $showDefaults, array(), array(), '', array(), array('GET'))->willReturn($showRoute);
        $routeCollection->add('sylius_product_show', $showRoute)->shouldBeCalled();

        $indexDefaults = array(
            '_controller' => 'sylius.controller.product:indexAction'
        );
        $routeFactory->createRoute('/products/', $indexDefaults, array(), array(), '', array(), array('GET'))->willReturn($indexRoute);
        $routeCollection->add('sylius_product_index', $indexRoute)->shouldBeCalled();

        $createDefaults = array(
            '_controller' => 'sylius.controller.product:createAction'
        );
        $routeFactory->createRoute('/products/new', $createDefaults, array(), array(), '', array(), array('GET', 'POST'))->willReturn($createRoute);
        $routeCollection->add('sylius_product_create', $createRoute)->shouldBeCalled();

        $updateDefaults = array(
            '_controller' => 'sylius.controller.product:updateAction'
        );
        $routeFactory->createRoute('/products/{id}/edit', $updateDefaults, array(), array(), '', array(), array('GET', 'PUT', 'PATCH'))->willReturn($updateRoute);
        $routeCollection->add('sylius_product_update', $updateRoute)->shouldBeCalled();

        $deleteDefaults = array(
            '_controller' => 'sylius.controller.product:deleteAction'
        );
        $routeFactory->createRoute('/products/{id}', $deleteDefaults, array(), array(), '', array(), array('DELETE'))->willReturn($deleteRoute);
        $routeCollection->add('sylius_product_delete', $deleteRoute)->shouldBeCalled();

        $this->load($configuration, 'sylius.resource')->shouldReturn($routeCollection);
    }

    function it_generates_urlized_paths_for_resources_with_multiple_words_in_name(
        RegistryInterface $resourceRegistry,
        MetadataInterface $metadata,
        RouteFactoryInterface $routeFactory,
        RouteCollection $routeCollection,
        Route $showRoute,
        Route $indexRoute,
        Route $createRoute,
        Route $updateRoute,
        Route $deleteRoute
    ) {
        $resourceRegistry->get('sylius.product_option')->willReturn($metadata);
        $metadata->getApplicationName()->willReturn('sylius');
        $metadata->getName()->willReturn('product_option');
        $metadata->getPluralName()->willReturn('product_options');
        $metadata->getServiceId('controller')->willReturn('sylius.controller.product_option');

        $routeFactory->createRouteCollection()->willReturn($routeCollection);

        $configuration =
<<<EOT
alias: sylius.product_option
EOT;

        $showDefaults = array(
            '_controller' => 'sylius.controller.product_option:showAction'
        );
        $routeFactory->createRoute('/product-options/{id}', $showDefaults, array(), array(), '', array(), array('GET'))->willReturn($showRoute);
        $routeCollection->add('sylius_product_option_show', $showRoute)->shouldBeCalled();

        $indexDefaults = array(
            '_controller' => 'sylius.controller.product_option:indexAction'
        );
        $routeFactory->createRoute('/product-options/', $indexDefaults, array(), array(), '', array(), array('GET'))->willReturn($indexRoute);
        $routeCollection->add('sylius_product_option_index', $indexRoute)->shouldBeCalled();

        $createDefaults = array(
            '_controller' => 'sylius.controller.product_option:createAction'
        );
        $routeFactory->createRoute('/product-options/new', $createDefaults, array(), array(), '', array(), array('GET', 'POST'))->willReturn($createRoute);
        $routeCollection->add('sylius_product_option_create', $createRoute)->shouldBeCalled();

        $updateDefaults = array(
            '_controller' => 'sylius.controller.product_option:updateAction'
        );
        $routeFactory->createRoute('/product-options/{id}/edit', $updateDefaults, array(), array(), '', array(), array('GET', 'PUT', 'PATCH'))->willReturn($updateRoute);
        $routeCollection->add('sylius_product_option_update', $updateRoute)->shouldBeCalled();

        $deleteDefaults = array(
            '_controller' => 'sylius.controller.product_option:deleteAction'
        );
        $routeFactory->createRoute('/product-options/{id}', $deleteDefaults, array(), array(), '', array(), array('DELETE'))->willReturn($deleteRoute);
        $routeCollection->add('sylius_product_option_delete', $deleteRoute)->shouldBeCalled();

        $this->load($configuration, 'sylius.resource')->shouldReturn($routeCollection);
    }

    function it_generates_routing_with_custom_path_if_specified(
        RegistryInterface $resourceRegistry,
        MetadataInterface $metadata,
        RouteFactoryInterface $routeFactory,
        RouteCollection $routeCollection,
        Route $showRoute,
        Route $indexRoute,
        Route $createRoute,
        Route $updateRoute,
        Route $deleteRoute
    ) {
        $resourceRegistry->get('sylius.product')->willReturn($metadata);
        $metadata->getApplicationName()->willReturn('sylius');
        $metadata->getName()->willReturn('product');
        $metadata->getPluralName()->willReturn('products');
        $metadata->getServiceId('controller')->willReturn('sylius.controller.product');

        $routeFactory->createRouteCollection()->willReturn($routeCollection);

        $configuration =
<<<EOT
alias: sylius.product
path: super-duper-products
EOT;

        $showDefaults = array(
            '_controller' => 'sylius.controller.product:showAction'
        );
        $routeFactory->createRoute('/super-duper-products/{id}', $showDefaults, array(), array(), '', array(), array('GET'))->willReturn($showRoute);
        $routeCollection->add('sylius_product_show', $showRoute)->shouldBeCalled();

        $indexDefaults = array(
            '_controller' => 'sylius.controller.product:indexAction'
        );
        $routeFactory->createRoute('/super-duper-products/', $indexDefaults, array(), array(), '', array(), array('GET'))->willReturn($indexRoute);
        $routeCollection->add('sylius_product_index', $indexRoute)->shouldBeCalled();

        $createDefaults = array(
            '_controller' => 'sylius.controller.product:createAction'
        );
        $routeFactory->createRoute('/super-duper-products/new', $createDefaults, array(), array(), '', array(), array('GET', 'POST'))->willReturn($createRoute);
        $routeCollection->add('sylius_product_create', $createRoute)->shouldBeCalled();

        $updateDefaults = array(
            '_controller' => 'sylius.controller.product:updateAction'
        );
        $routeFactory->createRoute('/super-duper-products/{id}/edit', $updateDefaults, array(), array(), '', array(), array('GET', 'PUT', 'PATCH'))->willReturn($updateRoute);
        $routeCollection->add('sylius_product_update', $updateRoute)->shouldBeCalled();

        $deleteDefaults = array(
            '_controller' => 'sylius.controller.product:deleteAction'
        );
        $routeFactory->createRoute('/super-duper-products/{id}', $deleteDefaults, array(), array(), '', array(), array('DELETE'))->willReturn($deleteRoute);
        $routeCollection->add('sylius_product_delete', $deleteRoute)->shouldBeCalled();

        $this->load($configuration, 'sylius.resource')->shouldReturn($routeCollection);
    }

    function it_generates_routing_with_custom_form_if_specified(
        RegistryInterface $resourceRegistry,
        MetadataInterface $metadata,
        RouteFactoryInterface $routeFactory,
        RouteCollection $routeCollection,
        Route $showRoute,
        Route $indexRoute,
        Route $createRoute,
        Route $updateRoute,
        Route $deleteRoute
    ) {
        $resourceRegistry->get('sylius.product')->willReturn($metadata);
        $metadata->getApplicationName()->willReturn('sylius');
        $metadata->getName()->willReturn('product');
        $metadata->getPluralName()->willReturn('products');
        $metadata->getServiceId('controller')->willReturn('sylius.controller.product');

        $routeFactory->createRouteCollection()->willReturn($routeCollection);

        $configuration =
<<<EOT
alias: sylius.product
form: sylius_product_custom
EOT;

        $showDefaults = array(
            '_controller' => 'sylius.controller.product:showAction'
        );
        $routeFactory->createRoute('/products/{id}', $showDefaults, array(), array(), '', array(), array('GET'))->willReturn($showRoute);
        $routeCollection->add('sylius_product_show', $showRoute)->shouldBeCalled();

        $indexDefaults = array(
            '_controller' => 'sylius.controller.product:indexAction'
        );
        $routeFactory->createRoute('/products/', $indexDefaults, array(), array(), '', array(), array('GET'))->willReturn($indexRoute);
        $routeCollection->add('sylius_product_index', $indexRoute)->shouldBeCalled();

        $createDefaults = array(
            '_controller' => 'sylius.controller.product:createAction',
            '_sylius' => array(
                'form' => 'sylius_product_custom',
            )
        );
        $routeFactory->createRoute('/products/new', $createDefaults, array(), array(), '', array(), array('GET', 'POST'))->willReturn($createRoute);
        $routeCollection->add('sylius_product_create', $createRoute)->shouldBeCalled();

        $updateDefaults = array(
            '_controller' => 'sylius.controller.product:updateAction',
            '_sylius' => array(
                'form' => 'sylius_product_custom',
            )
        );
        $routeFactory->createRoute('/products/{id}/edit', $updateDefaults, array(), array(), '', array(), array('GET', 'PUT', 'PATCH'))->willReturn($updateRoute);
        $routeCollection->add('sylius_product_update', $updateRoute)->shouldBeCalled();

        $deleteDefaults = array(
            '_controller' => 'sylius.controller.product:deleteAction'
        );
        $routeFactory->createRoute('/products/{id}', $deleteDefaults, array(), array(), '', array(), array('DELETE'))->willReturn($deleteRoute);
        $routeCollection->add('sylius_product_delete', $deleteRoute)->shouldBeCalled();

        $this->load($configuration, 'sylius.resource')->shouldReturn($routeCollection);
    }

    function it_supports_sylius_resource_type()
    {
        $this->supports('sylius.product', 'sylius.resource')->shouldReturn(true);
        $this->supports('sylius.product', 'abc')->shouldReturn(false);
    }
}
