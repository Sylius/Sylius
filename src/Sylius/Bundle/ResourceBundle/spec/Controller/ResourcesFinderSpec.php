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

use Pagerfanta\Pagerfanta;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\ResourceBundle\Controller\RequestConfiguration;
use Sylius\Component\Contact\Model\Request;
use Sylius\Component\Resource\Model\ResourceInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\HttpFoundation\ParameterBag;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class ResourcesFinderSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\ResourceBundle\Controller\ResourcesFinder');
    }
    
    function it_implements_resources_finder_interface()
    {
        $this->shouldImplement('Sylius\Bundle\ResourceBundle\Controller\ResourcesFinderInterface');
    }

    function it_gets_all_resources_if_not_paginated_and_there_is_no_limit(
        RequestConfiguration $requestConfiguration,
        RepositoryInterface $repository,
        ResourceInterface $resource1,
        ResourceInterface $resource2
    )
    {
        $requestConfiguration->getRepositoryMethod(null)->willReturn(null);

        $requestConfiguration->isPaginated()->willReturn(false);
        $requestConfiguration->isLimited()->willReturn(false);
        
        $repository->findAll()->willReturn(array($resource1, $resource2));
        
        $this->findCollection($requestConfiguration, $repository)->shouldReturn(array($resource1, $resource2));
    }

    function it_finds_resources_by_criteria_if_not_paginated(
        RequestConfiguration $requestConfiguration,
        RepositoryInterface $repository,
        ResourceInterface $resource1,
        ResourceInterface $resource2,
        ResourceInterface $resource3
    )
    {
        $requestConfiguration->getRepositoryMethod(null)->willReturn(null);

        $requestConfiguration->isPaginated()->willReturn(false);
        $requestConfiguration->isLimited()->willReturn(true);
        $requestConfiguration->getLimit()->willReturn(15);
        
        $requestConfiguration->getCriteria()->willReturn(array('custom' => 'criteria'));
        $requestConfiguration->getSorting()->willReturn(array('name' => 'desc'));

        $repository->findBy(array('custom' => 'criteria'), array('name' => 'desc'), 15)->willReturn(array($resource1, $resource2, $resource3));;

        $this->findCollection($requestConfiguration, $repository)->shouldReturn(array($resource1, $resource2, $resource3));
    }

    function it_uses_custom_method_and_arguments_if_specified(
        RequestConfiguration $requestConfiguration,
        RepositoryInterface $repository,
        ResourceInterface $resource1
    )
    {
        $requestConfiguration->getRepositoryMethod(null)->willReturn('findAll');
        $requestConfiguration->getRepositoryArguments(array())->willReturn(array('foo'));

        $requestConfiguration->isPaginated()->willReturn(false);
        $requestConfiguration->isLimited()->willReturn(true);
        $requestConfiguration->getLimit()->willReturn(15);

        $repository->findAll('foo')->willReturn(array($resource1));;

        $this->findCollection($requestConfiguration, $repository)->shouldReturn(array($resource1));
    }

    function it_creates_paginator_by_default(
        RequestConfiguration $requestConfiguration,
        RepositoryInterface $repository,
        Pagerfanta $paginator,
        Request $request,
        ParameterBag $queryParameters
    )
    {

        $requestConfiguration->getRepositoryMethod(null)->willReturn(null);

        $requestConfiguration->isPaginated()->willReturn(true);
        $requestConfiguration->isLimited()->willReturn(false);
        $requestConfiguration->getCriteria()->willReturn(array());
        $requestConfiguration->getSorting()->willReturn(array());

        $repository->createPaginator(array(), array())->willReturn($paginator);
       
        $requestConfiguration->getRequest()->willReturn($request);
        $request->query = $queryParameters;
        $queryParameters->get('page', 1)->willReturn(6);

        $paginator->setCurrentPage(6)->shouldBeCalled();

        $this->findCollection($requestConfiguration, $repository)->shouldReturn($paginator);
    }
}
