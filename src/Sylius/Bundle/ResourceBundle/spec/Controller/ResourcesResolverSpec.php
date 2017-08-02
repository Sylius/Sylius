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

namespace spec\Sylius\Bundle\ResourceBundle\Controller;

use Pagerfanta\Pagerfanta;
use PhpSpec\ObjectBehavior;
use Sylius\Bundle\ResourceBundle\Controller\RequestConfiguration;
use Sylius\Bundle\ResourceBundle\Controller\ResourcesResolver;
use Sylius\Bundle\ResourceBundle\Controller\ResourcesResolverInterface;
use Sylius\Component\Resource\Model\ResourceInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
final class ResourcesResolverSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(ResourcesResolver::class);
    }

    function it_implements_resources_resolver_interface()
    {
        $this->shouldImplement(ResourcesResolverInterface::class);
    }

    function it_gets_all_resources_if_has_no_criteria(
        RequestConfiguration $requestConfiguration,
        RepositoryInterface $repository,
        ResourceInterface $firstResource,
        ResourceInterface $secondResource
    ) {
        $requestConfiguration->isHtmlRequest()->willReturn(true);
        $requestConfiguration->getRepositoryMethod(null)->willReturn(null);

        $requestConfiguration->isPaginated()->willReturn(false);
        $requestConfiguration->isFilterable()->willReturn(false);
        $requestConfiguration->isSortable()->willReturn(false);
        $requestConfiguration->getLimit()->willReturn(null);

        $repository->findBy([], [], null)->willReturn([$firstResource, $secondResource]);

        $this->getResources($requestConfiguration, $repository)->shouldReturn([$firstResource, $secondResource]);
    }

    function it_finds_resources_by_criteria_if_not_paginated(
        RequestConfiguration $requestConfiguration,
        RepositoryInterface $repository,
        ResourceInterface $firstResource,
        ResourceInterface $secondResource,
        ResourceInterface $thirdResource
    ) {
        $requestConfiguration->isHtmlRequest()->willReturn(true);
        $requestConfiguration->getRepositoryMethod(null)->willReturn(null);

        $requestConfiguration->isPaginated()->willReturn(false);
        $requestConfiguration->isFilterable()->willReturn(true);
        $requestConfiguration->isSortable()->willReturn(true);
        $requestConfiguration->isLimited()->willReturn(true);
        $requestConfiguration->getLimit()->willReturn(15);

        $requestConfiguration->getCriteria()->willReturn(['custom' => 'criteria']);
        $requestConfiguration->getSorting()->willReturn(['name' => 'desc']);

        $repository->findBy(['custom' => 'criteria'], ['name' => 'desc'], 15)->willReturn([$firstResource, $secondResource, $thirdResource]);

        $this->getResources($requestConfiguration, $repository)->shouldReturn([$firstResource, $secondResource, $thirdResource]);
    }

    function it_uses_custom_method_and_arguments_if_specified(
        RequestConfiguration $requestConfiguration,
        RepositoryInterface $repository,
        ResourceInterface $firstResource
    ) {
        $requestConfiguration->isHtmlRequest()->willReturn(true);
        $requestConfiguration->getRepositoryMethod()->willReturn('findAll');
        $requestConfiguration->getRepositoryArguments()->willReturn(['foo']);

        $requestConfiguration->isPaginated()->willReturn(false);
        $requestConfiguration->isLimited()->willReturn(true);
        $requestConfiguration->getLimit()->willReturn(15);

        $repository->findAll('foo')->willReturn([$firstResource]);

        $this->getResources($requestConfiguration, $repository)->shouldReturn([$firstResource]);
    }

    function it_creates_paginator_by_default(
        RequestConfiguration $requestConfiguration,
        RepositoryInterface $repository,
        Pagerfanta $paginator
    ) {
        $requestConfiguration->isHtmlRequest()->willReturn(true);
        $requestConfiguration->getRepositoryMethod()->willReturn(null);

        $requestConfiguration->isPaginated()->willReturn(true);
        $requestConfiguration->getPaginationMaxPerPage()->willReturn(5);
        $requestConfiguration->isLimited()->willReturn(false);
        $requestConfiguration->isFilterable()->willReturn(false);
        $requestConfiguration->isSortable()->willReturn(false);

        $repository->createPaginator([], [])->willReturn($paginator);

        $this->getResources($requestConfiguration, $repository)->shouldReturn($paginator);
    }
}

