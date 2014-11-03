<?php

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
        $dm->getUnitOfWork()->shouldBeCalled()->willReturn($uow);

        $class->name = 'Sylius\Component\Core\Model\Product';
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\ResourceBundle\Doctrine\ODM\PHPCR\DocumentRepository');
    }

    function it_creates_instance()
    {
        $this->createNew()->shouldHaveType('Sylius\Component\Core\Model\Product');
    }

    function it_has_a_paginator(QueryBuilder $queryBuilder)
    {
        $this->getPaginator($queryBuilder)->shouldHaveType('Pagerfanta\Pagerfanta');
    }
}
