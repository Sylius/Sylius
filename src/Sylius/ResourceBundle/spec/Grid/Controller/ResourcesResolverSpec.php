<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\ResourceBundle\Grid\Controller;

use Pagerfanta\Pagerfanta;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\ResourceBundle\Controller\RequestConfiguration;
use Sylius\ResourceBundle\Controller\ResourcesResolverInterface;
use Sylius\ResourceBundle\Grid\Controller\ResourcesResolver;
use Sylius\ResourceBundle\Grid\View\ResourceGridView;
use Sylius\ResourceBundle\Grid\View\ResourceGridViewFactoryInterface;
use Sylius\Grid\Definition\Grid;
use Sylius\Grid\Parameters;
use Sylius\Grid\Provider\GridProviderInterface;
use Sylius\Resource\Metadata\MetadataInterface;
use Sylius\Resource\Model\ResourceInterface;
use Sylius\Resource\Repository\RepositoryInterface;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;

/**
 * @mixin ResourcesResolver
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class ResourcesResolverSpec extends ObjectBehavior
{
    function let(
        ResourcesResolverInterface $decoratedResolver,
        GridProviderInterface $gridProvider,
        ResourceGridViewFactoryInterface $gridViewFactory
    ) {
        $this->beConstructedWith($decoratedResolver, $gridProvider, $gridViewFactory);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\ResourceBundle\Grid\Controller\ResourcesResolver');
    }
    
    function it_implements_resources_resolver_interface()
    {
        $this->shouldImplement(ResourcesResolverInterface::class);
    }

    function it_uses_decorated_resolver_when_not_using_a_grid(
        ResourcesResolverInterface $decoratedResolver,
        RequestConfiguration $requestConfiguration,
        RepositoryInterface $repository,
        ResourceInterface $resource
    ) {
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
    ) {
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
    ) {
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
