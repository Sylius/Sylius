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
        $requestConfiguration->isPaginated()->willReturn(false);
        $requestConfiguration->isLimited()->willReturn(false);
        
        $repository->findAll()->willReturn(array($resource1, $resource2));
        
        $this->findCollection($requestConfiguration, $repository)->shouldReturn(array($resource1, $resource2));
    }
}
