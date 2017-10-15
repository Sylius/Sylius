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

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\ResourceBundle\Controller\RequestConfiguration;
use Sylius\Bundle\ResourceBundle\Controller\SingleResourceProviderInterface;
use Sylius\Component\Resource\Model\ResourceInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
final class SingleResourceProviderSpec extends ObjectBehavior
{
    function it_implements_single_resource_provider_interface(): void
    {
        $this->shouldImplement(SingleResourceProviderInterface::class);
    }

    function it_looks_for_specific_resource_with_id_by_default(
        RequestConfiguration $requestConfiguration,
        Request $request,
        ParameterBag $requestAttributes,
        RepositoryInterface $repository
    ): void {
        $requestConfiguration->getCriteria()->willReturn([]);
        $requestConfiguration->getRepositoryMethod()->willReturn(null);
        $requestConfiguration->getRequest()->willReturn($request);
        $request->attributes = $requestAttributes;
        $requestAttributes->has('id')->willReturn(true);
        $requestAttributes->has('slug')->willReturn(false);
        $requestAttributes->get('id')->willReturn(5);

        $repository->find(5)->willReturn(null);

        $this->get($requestConfiguration, $repository)->shouldReturn(null);
    }

    function it_can_find_specific_resource_with_id_by_default(
        RequestConfiguration $requestConfiguration,
        Request $request,
        ParameterBag $requestAttributes,
        RepositoryInterface $repository,
        ResourceInterface $resource
    ): void {
        $requestConfiguration->getCriteria()->willReturn([]);
        $requestConfiguration->getRepositoryMethod()->willReturn(null);
        $requestConfiguration->getRequest()->willReturn($request);
        $request->attributes = $requestAttributes;
        $requestAttributes->has('id')->willReturn(true);
        $requestAttributes->has('slug')->willReturn(false);
        $requestAttributes->get('id')->willReturn(3);

        $repository->find(3)->willReturn($resource);

        $this->get($requestConfiguration, $repository)->shouldReturn($resource);
    }

    function it_can_find_specific_resource_with_slug_by_default(
        RequestConfiguration $requestConfiguration,
        Request $request,
        ParameterBag $requestAttributes,
        RepositoryInterface $repository,
        ResourceInterface $resource
    ): void {
        $requestConfiguration->getCriteria()->willReturn([]);
        $requestConfiguration->getRepositoryMethod()->willReturn(null);
        $requestConfiguration->getRequest()->willReturn($request);
        $request->attributes = $requestAttributes;
        $requestAttributes->has('id')->willReturn(false);
        $requestAttributes->has('slug')->willReturn(true);
        $requestAttributes->get('slug')->willReturn('the-most-awesome-hat');

        $repository->findOneBy(['slug' => 'the-most-awesome-hat'])->willReturn($resource);

        $this->get($requestConfiguration, $repository)->shouldReturn($resource);
    }

    function it_can_find_specific_resource_with_custom_criteria(
        RequestConfiguration $requestConfiguration,
        Request $request,
        ParameterBag $requestAttributes,
        RepositoryInterface $repository,
        ResourceInterface $resource
    ): void {
        $requestConfiguration->getCriteria()->willReturn(['request-configuration-criteria' => '1']);
        $requestConfiguration->getRepositoryMethod()->willReturn(null);
        $requestConfiguration->getRequest()->willReturn($request);
        $request->attributes = $requestAttributes;
        $requestAttributes->has('id')->willReturn(false);
        $requestAttributes->has('slug')->willReturn(false);

        $repository->findOneBy(['request-configuration-criteria' => '1'])->willReturn($resource);

        $this->get($requestConfiguration, $repository)->shouldReturn($resource);
    }

    function it_can_find_specific_resource_with_merged_custom_criteria(
        RequestConfiguration $requestConfiguration,
        Request $request,
        ParameterBag $requestAttributes,
        RepositoryInterface $repository,
        ResourceInterface $resource
    ): void {
        $requestConfiguration->getCriteria()->willReturn(['request-configuration-criteria' => '1']);
        $requestConfiguration->getRepositoryMethod()->willReturn(null);
        $requestConfiguration->getRequest()->willReturn($request);
        $request->attributes = $requestAttributes;
        $requestAttributes->has('id')->willReturn(false);
        $requestAttributes->has('slug')->willReturn(true);
        $requestAttributes->get('slug')->willReturn('banana');

        $repository->findOneBy(['slug' => 'banana', 'request-configuration-criteria' => '1'])->willReturn($resource);

        $this->get($requestConfiguration, $repository)->shouldReturn($resource);
    }

    function it_can_find_specific_resource_with_merged_custom_criteria_overwriting_the_attributes(
        RequestConfiguration $requestConfiguration,
        Request $request,
        ParameterBag $requestAttributes,
        RepositoryInterface $repository,
        ResourceInterface $resource
    ): void {
        $requestConfiguration->getCriteria()->willReturn(['id' => 5]);
        $requestConfiguration->getRepositoryMethod()->willReturn(null);
        $requestConfiguration->getRequest()->willReturn($request);
        $request->attributes = $requestAttributes;
        $requestAttributes->has('id')->willReturn(false);
        $requestAttributes->has('slug')->willReturn(false);

        $repository->findOneBy(['id' => 5])->willReturn($resource);

        $this->get($requestConfiguration, $repository)->shouldReturn($resource);
    }

    function it_uses_a_custom_method_if_configured(
        RequestConfiguration $requestConfiguration,
        RepositoryInterface $repository,
        ResourceInterface $resource
    ): void {
        $requestConfiguration->getRepositoryMethod()->willReturn('findAll');
        $requestConfiguration->getRepositoryArguments()->willReturn(['foo']);

        $repository->findAll('foo')->willReturn($resource);

        $this->get($requestConfiguration, $repository)->shouldReturn($resource);
    }
}
