<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\ResourceBundle\Routing\Loader;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\ResourceBundle\Routing\Builder\RouteCollectionBuilderInterface;
use Symfony\Component\Routing\RouteCollection;

/**
 * @author Arnaud Langlade <arn0d.dev@gmail.com>
 */
class GenericLoaderSpec extends ObjectBehavior
{
    function let(RouteCollectionBuilderInterface $collectionBuilder)
    {
        $this->beConstructedWith(
            $collectionBuilder,
            array(
                'sylius' => array(
                    'product' => '',
                )
            ),
            array(
                'sylius' => array(
                    'prefix' => 'backend'
                )
            )
        );
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\ResourceBundle\Routing\Loader\GenericLoader');
    }

    function it_is_a_loader()
    {
        $this->shouldHaveType('Sylius\Bundle\ResourceBundle\Routing\Loader\AbstractLoader');
    }

    function it_creates_routes($collectionBuilder, RouteCollection $route)
    {
        $collectionBuilder->createCollection('sylius', 'backend')->shouldBeCalled();

        $collectionBuilder->add('product', 'index', array('GET'))->shouldBeCalled();
        $collectionBuilder->add('product', 'show', array('GET'))->shouldBeCalled();
        $collectionBuilder->add('product', 'create', array('POST', 'GET'))->shouldBeCalled();
        $collectionBuilder->add('product', 'update', array('POST', 'GET'))->shouldBeCalled();
        $collectionBuilder->add('product', 'delete', array('DELETE'))->shouldBeCalled();

        $collectionBuilder->getCollection()->shouldBeCalled()->willReturn($route);

        $this->load(array('application' => 'sylius', 'prefix' => 'backend'))->shouldReturn($route);
    }

    function it_supports_the_loads()
    {
        $this->supports(array(), 'sylius.api')->shouldReturn(false);
        $this->supports(array(), 'sylius')->shouldReturn(true);
    }
}
