<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\ResourceBundle\Controller;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\ResourceBundle\Controller\RequestConfiguration;
use Sylius\Bundle\ResourceBundle\Controller\SingleResourceProviderInterface;
use Sylius\Component\Resource\Model\ResourceInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class SingleResourceProviderSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\ResourceBundle\Controller\SingleResourceProvider');
    }
    
    function it_implements_single_resource_provider_interface()
    {
        $this->shouldImplement(SingleResourceProviderInterface::class);
    }

    function it_looks_for_specific_resource_with_id_by_default(
        RequestConfiguration $requestConfiguration,
        Request $request,
        ParameterBag $requestAttributes,
        RepositoryInterface $repository
    )
    {
        $requestConfiguration->getRequest()->willReturn($request);
        $request->attributes = $requestAttributes;
        $requestAttributes->has('id')->willReturn(true);
        $requestAttributes->get('id')->willReturn(5);
        
        $repository->findOneBy(array('id' => 5))->willReturn(null);

        $this->get($requestConfiguration, $repository)->shouldReturn(null);
    }

    function it_can_find_specific_resource_with_id_by_default(
        RequestConfiguration $requestConfiguration,
        Request $request,
        ParameterBag $requestAttributes,
        RepositoryInterface $repository,
        ResourceInterface $resource
    )
    {
        $requestConfiguration->getRequest()->willReturn($request);
        $request->attributes = $requestAttributes;
        $requestAttributes->has('id')->willReturn(true);
        $requestAttributes->get('id')->willReturn(3);

        $repository->findOneBy(array('id' => 3))->willReturn($resource);

        $this->get($requestConfiguration, $repository)->shouldReturn($resource);
    }
}
