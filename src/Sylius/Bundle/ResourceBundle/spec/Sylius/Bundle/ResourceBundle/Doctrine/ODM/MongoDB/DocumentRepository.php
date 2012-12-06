<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\ResourceBundle\Doctrine\ODM\MongoDB;

use PHPSpec2\ObjectBehavior;

/**
 * Doctrine ODM driver document repository spec.
 *
 * @author Саша Стаменковић <umpirsky@gmail.com>
 */
class DocumentRepository extends ObjectBehavior
{
    /**
     * @param Doctrine\ODM\MongoDB\DocumentManager       $documentManager
     * @param Doctrine\ODM\MongoDB\UnitOfWork            $unitOfWork
     * @param Doctrine\ODM\MongoDB\Mapping\ClassMetadata $class
     * @param Doctrine\ODM\MongoDB\Query\Builder         $queryBuilder
     */
    function let($documentManager, $unitOfWork, $class, $queryBuilder)
    {
        $class->name = 'spec\Sylius\Bundle\ResourceBundle\Fixture\Entity\Foo';

        $documentManager
            ->createQueryBuilder($class->name)
            ->willReturn($queryBuilder)
        ;

        $this->beConstructedWith($documentManager, $unitOfWork, $class);
    }

    function it_should_be_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\ResourceBundle\Doctrine\ODM\MongoDB\DocumentRepository');
        $this->shouldHaveType('Doctrine\ODM\MongoDB\DocumentRepository');
    }

    function it_should_create_paginator()
    {
        $this
            ->createPaginator()
            ->shouldHaveType('Pagerfanta\Pagerfanta')
        ;
    }
}
