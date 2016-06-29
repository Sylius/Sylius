<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\ResourceBundle\Grid\View;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\ResourceBundle\Controller\RequestConfiguration;
use Sylius\ResourceBundle\Grid\View\ResourceGridView;
use Sylius\Grid\Definition\Grid;
use Sylius\Grid\Parameters;
use Sylius\Grid\View\GridView;
use Sylius\Resource\Metadata\MetadataInterface;

/**
 * @mixin ResourceGridView
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class ResourceGridViewSpec extends ObjectBehavior
{
    function let(
        Grid $gridDefinition,
        Parameters $parameters,
        MetadataInterface $resourceMetadata,
        RequestConfiguration $requestConfiguration
    ) {
        $this->beConstructedWith(['foo', 'bar'], $gridDefinition, $parameters, $resourceMetadata, $requestConfiguration);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\ResourceBundle\Grid\View\ResourceGridView');
    }

    function it_extends_default_GridView()
    {
        $this->shouldHaveType(GridView::class);
    }

    function it_has_resource_metadata(MetadataInterface $resourceMetadata)
    {
        $this->getMetadata()->shouldReturn($resourceMetadata);
    }
    
    function it_has_request_configuration(RequestConfiguration $requestConfiguration)
    {
        $this->getRequestConfiguration()->shouldReturn($requestConfiguration);
    }
}
