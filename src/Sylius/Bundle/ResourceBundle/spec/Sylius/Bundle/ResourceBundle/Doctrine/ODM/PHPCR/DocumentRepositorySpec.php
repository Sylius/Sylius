<?php

namespace spec\Sylius\Bundle\ResourceBundle\Doctrine\ODM\PHPCR;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Doctrine\ODM\PHPCR\Mapping\ClassMetadata;
use Doctrine\ODM\PHPCR\DocumentManager;
use Doctrine\ODM\PHPCR\Query\Builder\QueryBuilder;

class DocumentRepositorySpec extends ObjectBehavior
{
    function let(DocumentManager $dm, ClassMetadata $class)
    {
        $this->beConstructedWith($dm, $class);

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

    function it_creates_a_paginator($dm, QueryBuilder $queryBuilder)
    {
        $dm->createQueryBuilder()->willReturn($queryBuilder);
        $queryBuilder->from('o')->willReturn($queryBuilder);
        $queryBuilder->document('Sylius\Component\Core\Model\Product', 'o')->willReturn($queryBuilder);

        $this->createPaginator()->shouldHaveType('Pagerfanta\Pagerfanta');
    }

    function it_has_a_paginator(QueryBuilder $queryBuilder)
    {
        $this->getPaginator($queryBuilder)->shouldHaveType('Pagerfanta\Pagerfanta');
    }
}
