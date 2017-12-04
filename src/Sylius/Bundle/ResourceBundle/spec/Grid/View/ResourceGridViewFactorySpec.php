<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace spec\Sylius\Bundle\ResourceBundle\Grid\View;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\ResourceBundle\Controller\ParametersParserInterface;
use Sylius\Bundle\ResourceBundle\Controller\RequestConfiguration;
use Sylius\Bundle\ResourceBundle\Grid\View\ResourceGridView;
use Sylius\Bundle\ResourceBundle\Grid\View\ResourceGridViewFactoryInterface;
use Sylius\Component\Grid\Data\DataProviderInterface;
use Sylius\Component\Grid\Definition\Grid;
use Sylius\Component\Grid\Parameters;
use Sylius\Component\Resource\Metadata\MetadataInterface;
use Symfony\Component\HttpFoundation\Request;

final class ResourceGridViewFactorySpec extends ObjectBehavior
{
    function let(DataProviderInterface $dataProvider, ParametersParserInterface $parametersParser): void
    {
        $this->beConstructedWith($dataProvider, $parametersParser);
    }

    function it_implements_resource_grid_view_factory_interface(): void
    {
        $this->shouldImplement(ResourceGridViewFactoryInterface::class);
    }

    function it_uses_data_provider_to_create_a_view_with_data_and_definition(
        DataProviderInterface $dataProvider,
        ParametersParserInterface $parametersParser,
        Grid $grid,
        MetadataInterface $resourceMetadata,
        Request $request,
        RequestConfiguration $requestConfiguration
    ): void {
        $parameters = new Parameters();

        $expectedResourceGridView = new ResourceGridView(
            ['foo', 'bar'],
            $grid->getWrappedObject(),
            $parameters,
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

        $this->create($grid, $parameters, $resourceMetadata, $requestConfiguration)->shouldBeLike($expectedResourceGridView);
    }
}
