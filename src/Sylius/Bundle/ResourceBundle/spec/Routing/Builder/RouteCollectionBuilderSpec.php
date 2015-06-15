<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\ResourceBundle\Routing\Builder;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

/**
 * @author Arnaud Langlade <arn0d.dev@gmail.com>
 */
class RouteCollectionBuilderSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\ResourceBundle\Routing\Builder\RouteCollectionBuilder');
    }

    function it_is_a_builder()
    {
        $this->shouldImplement('Sylius\Bundle\ResourceBundle\Routing\Builder\RouteCollectionBuilderInterface');
    }

    function it_creates_a_new_collection()
    {
        $this->createCollection('sylius', 'api');
    }

    function it_adds_a_route()
    {
        $this->createCollection('sylius', 'api');
        $this->add('product', 'create', array('GET'));
    }

    function it_returns_the_collection()
    {
        $this->createCollection('sylius', 'api');
        $this->getCollection()->shouldHaveType('Symfony\Component\Routing\RouteCollection');
    }
}
