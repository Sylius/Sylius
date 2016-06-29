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
use Sylius\ResourceBundle\Controller\ParametersParserInterface;
use Sylius\ResourceBundle\Controller\RequestConfiguration;
use Sylius\ResourceBundle\Grid\View\ResourceGridView;
use Sylius\ResourceBundle\Grid\View\ResourceGridViewFactory;
use Sylius\ResourceBundle\Grid\View\ResourceGridViewFactoryInterface;
use Sylius\Grid\Data\DataProviderInterface;
use Sylius\Grid\Definition\Grid;
use Sylius\Grid\Parameters;
use Sylius\Resource\Metadata\MetadataInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * @mixin ResourceGridViewFactory
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class ResourceGridViewFactorySpec extends ObjectBehavior
{
    function let(DataProviderInterface $dataProvider, ParametersParserInterface $parametersParser)
    {
        $this->beConstructedWith($dataProvider, $parametersParser);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\ResourceBundle\Grid\View\ResourceGridViewFactory');
    }

    function it_implements_resource_grid_view_factory_interface()
    {
        $this->shouldImplement(ResourceGridViewFactoryInterface::class);
    }

    function it_uses_data_provider_to_create_a_view_with_data_and_definition(
        DataProviderInterface $dataProvider,
        ParametersParserInterface $parametersParser,
        Grid $grid,
        Parameters $parameters,
        MetadataInterface $resourceMetadata,
        Request $request,
        RequestConfiguration $requestConfiguration
    ) {
        $expectedResourceGridView = new ResourceGridView(
            ['foo', 'bar'],
            $grid->getWrappedObject(),
            $parameters->getWrappedObject(),
            $resourceMetadata->getWrappedObject(),
            $requestConfiguration->getWrappedObject()
        );

        $requestConfiguration->getRequest()->willReturn($request);
        $parametersParser
            ->parseRequestValues(['repository' => ['method' => 'createByCustomerQueryBuilder', 'arguments' => ['$customerId']]], $request)
            ->willReturn(['repository' => ['method' => 'createByCustomerQueryBuilder', 'arguments' => [5]]])
        ;

        $grid->getDriverConfiguration()->willReturn(['repository' => ['method' => 'createByCustomerQueryBuilder', 'arguments' => ['$customerId']]]);
        $grid->setDriverConfiguration(['repository' => ['method' => 'createByCustomerQueryBuilder', 'arguments' => [5]]])->shouldBeCalled();

        $dataProvider->getData($grid, $parameters)->willReturn(['foo', 'bar']);

        $this->create($grid, $parameters, $resourceMetadata, $requestConfiguration)->shouldBeSameResourceGridViewAs($expectedResourceGridView);
    }

    public function getMatchers()
    {
        return [
            'beSameResourceGridViewAs' => function ($subject, $key) {
                if (!$subject instanceof ResourceGridView || !$key instanceof ResourceGridView) {
                    return false;
                }

                return serialize($subject) === serialize($key);
            },
        ];
    }
}

