<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\ResourceBundle\Doctrine\ODM\PHPCR;

use Doctrine\ODM\PHPCR\DocumentManager;
use Doctrine\ODM\PHPCR\Mapping\ClassMetadata;
use Doctrine\ODM\PHPCR\Query\Builder\QueryBuilder;
use Doctrine\ODM\PHPCR\UnitOfWork;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class DocumentRepositorySpec extends ObjectBehavior
{
    function let(DocumentManager $dm, ClassMetadata $class, UnitOfWork $uow)
    {
        $this->beConstructedWith($dm, $class);
        $dm->getUnitOfWork()->willReturn($uow);

        $class->name = 'Sylius\Component\Core\Model\Product';
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\ResourceBundle\Doctrine\ODM\PHPCR\DocumentRepository');
    }

    function it_has_a_paginator(QueryBuilder $queryBuilder)
    {
        $this->getPaginator($queryBuilder)->shouldHaveType('Pagerfanta\Pagerfanta');
    }
}
