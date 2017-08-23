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

namespace spec\Sylius\Bundle\ResourceBundle\Grid\Controller;

use Pagerfanta\Pagerfanta;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\ResourceBundle\Controller\RequestConfiguration;
use Sylius\Bundle\ResourceBundle\Controller\ResourcesResolverInterface;
use Sylius\Bundle\ResourceBundle\Grid\Controller\ResourcesResolver;
use Sylius\Bundle\ResourceBundle\Grid\View\ResourceGridView;
use Sylius\Bundle\ResourceBundle\Grid\View\ResourceGridViewFactoryInterface;
use Sylius\Component\Grid\Definition\Grid;
use Sylius\Component\Grid\Parameters;
use Sylius\Component\Grid\Provider\GridProviderInterface;
use Sylius\Component\Resource\Metadata\MetadataInterface;
use Sylius\Component\Resource\Model\ResourceInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
final class ResourcesResolverSpec extends ObjectBehavior
{
    function let(
        ResourcesResolverInterface $decoratedResolver,
        GridProviderInterface $gridProvider,
        ResourceGridViewFactoryInterface $gridViewFactory
    ): void {
        $this->beConstructedWith($decoratedResolver, $gridProvider, $gridViewFactory);
    }

    function it_implements_resources_resolver_interface(): void
    {
        $this->shouldImplement(ResourcesResolverInterface::class);
    }

    function it_uses_decorated_resolver_when_not_using_a_grid(
        ResourcesResolverInterface $decoratedResolver,
        RequestConfiguration $requestConfiguration,
        RepositoryInterface $repository,
        ResourceInterface $resource
    ): void {
        $requestConfiguration->hasGrid()->willReturn(false);

        $decoratedResolver->getResources($requestConfiguration, $repository)->willReturn([$resource]);

        $this->getResources($requestConfiguration, $repository)->shouldReturn([$resource]);
    }

    function it_returns_grid_view(
        RequestConfiguration $requestConfiguration,
        RepositoryInterface $repository,
        Grid $gridDefinition,
        GridProviderInterface $gridProvider,
        ResourceGridViewFactoryInterface $gridViewFactory,
        ResourceGridView $gridView,
        MetadataInterface $metadata,
        Request $request,
        ParameterBag $queryParameters
    ): void {
        $requestConfiguration->hasGrid()->willReturn(true);
        $requestConfiguration->getGrid()->willReturn('sylius_admin_tax_category');
        $requestConfiguration->getMetadata()->willReturn($metadata);
        $requestConfiguration->isHtmlRequest()->willReturn(true);
        $requestConfiguration->getRequest()->willReturn($request);

        $request->query = $queryParameters;
        $queryParameters->all()->willReturn(['foo' => 'bar']);

        $gridProvider->get('sylius_admin_tax_category')->willReturn($gridDefinition);
        $gridViewFactory->create($gridDefinition, Argument::type(Parameters::class), $metadata, $requestConfiguration)->willReturn($gridView);

        $this->getResources($requestConfiguration, $repository)->shouldReturn($gridView);
    }

    function it_returns_grid_data_for_non_html_requests(
        RequestConfiguration $requestConfiguration,
        RepositoryInterface $repository,
        Grid $gridDefinition,
        GridProviderInterface $gridProvider,
        ResourceGridViewFactoryInterface $gridViewFactory,
        ResourceGridView $gridView,
        Pagerfanta $paginator,
        MetadataInterface $metadata,
        Request $request,
        ParameterBag $queryParameters
    ): void {
        $requestConfiguration->hasGrid()->willReturn(true);
        $requestConfiguration->getGrid()->willReturn('sylius_admin_tax_category');
        $requestConfiguration->getMetadata()->willReturn($metadata);
        $requestConfiguration->isHtmlRequest()->willReturn(false);
        $requestConfiguration->getRequest()->willReturn($request);

        $request->query = $queryParameters;
        $queryParameters->all()->willReturn(['foo' => 'bar']);

        $gridProvider->get('sylius_admin_tax_category')->willReturn($gridDefinition);
        $gridViewFactory->create($gridDefinition, Argument::type(Parameters::class), $metadata, $requestConfiguration)->willReturn($gridView);
        $gridView->getData()->willReturn($paginator);

        $this->getResources($requestConfiguration, $repository)->shouldReturn($paginator);
    }
}
