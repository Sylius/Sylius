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
use Sylius\Component\Resource\Model\ResourceInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class ResourceFinderSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\ResourceBundle\Controller\ResourceFinder');
    }
    
    function it_implements_resource_finder_interface()
    {
        $this->shouldImplement('Sylius\Bundle\ResourceBundle\Controller\ResourceFinderInterface');
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

        $this->find($requestConfiguration, $repository)->shouldReturn(null);
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

        $this->find($requestConfiguration, $repository)->shouldReturn($resource);
    }
}
