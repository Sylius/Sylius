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

use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\Mapping\ClassMetadata;
use Doctrine\ODM\MongoDB\Query\Builder;
use Doctrine\ODM\MongoDB\Query\Query;
use Doctrine\ODM\MongoDB\UnitOfWork;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Component\Locale\Context\LocaleContextInterface;

require_once __DIR__.'/../../../Fixture/Document/TranslatableFoo.php';

/**
 * Doctrine ODM MongoDB driver translatable document repository spec.
 *
 * @author Ivannis Suárez Jérez <ivannis.suarez@gmail.com>
 */
class TranslatableDocumentRepositorySpec extends ObjectBehavior
{
    function let(
        DocumentManager $documentManager,
        UnitOfWork $unitOfWork,
        ClassMetadata $class,
        Builder $queryBuilder,
        Query $query
    ) {
        $class->name = 'spec\Sylius\Bundle\ResourceBundle\Fixture\Document\TranslatableFoo';

        $documentManager
            ->createQueryBuilder($class->name)
            ->willReturn($queryBuilder)
        ;

        $queryBuilder
            ->getQuery()
            ->willReturn($query)
        ;

        $this->beConstructedWith($documentManager, $unitOfWork, $class);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\ResourceBundle\Doctrine\ODm\MongoDB\TranslatableDocumentRepository');
    }

    function it_implements_Sylius_repository_interface()
    {
        $this->shouldImplement('Sylius\Bundle\ResourceBundle\Doctrine\TranslatableResourceRepositoryInterface');
    }

    function it_sets_current_locale_on_created_object(LocaleContextInterface $localeContext)
    {
        $localeContext->getLocale()->willReturn('en');

        $this->setLocaleContext($localeContext);

        $this->createNew()->getCurrentLocale()->shouldReturn('en');
    }

    function it_applies_criteria_when_finding_one($queryBuilder, LocaleContextInterface $localeContext)
    {
        $localeContext->getLocale()->willReturn('en');
        $this->setLocaleContext($localeContext);

        $translatableFields = array('foo');

        $this->setTranslatableFields($translatableFields);

        $criteria = array(
            'foo' => 'bar',
            'bar' => 'baz',
        );

        foreach ($criteria as $property => $value) {
            if (in_array($property, $translatableFields)) {
                $property = 'translations.en.' . $property;
            }

            $queryBuilder
                ->field($property)
                ->shouldBeCalled()
                ->willReturn($queryBuilder)
            ;

            $queryBuilder
                ->equals($value)
                ->shouldBeCalled()
                ->willReturn($queryBuilder)
            ;
        }

        $this->findOneBy($criteria)->shouldReturn(null);
    }

    function it_applies_criteria_when_finding_by($queryBuilder, LocaleContextInterface $localeContext)
    {
        $localeContext->getLocale()->willReturn('en');
        $this->setLocaleContext($localeContext);

        $translatableFields = array('foo');

        $this->setTranslatableFields($translatableFields);

        $criteria = array(
            'foo' => 'bar',
            'bar' => 'baz',
        );

        foreach ($criteria as $property => $value) {
            if (in_array($property, $translatableFields)) {
                $property = 'translations.en.' . $property;
            }

            $queryBuilder
                ->field($property)
                ->shouldBeCalled()
                ->willReturn($queryBuilder)
            ;

            $queryBuilder
                ->equals($value)
                ->shouldBeCalled()
                ->willReturn($queryBuilder)
            ;
        }

        $this->findBy($criteria)->shouldReturn(null);
    }

    function it_applies_criteria_when_finding_by_array($queryBuilder, LocaleContextInterface $localeContext)
    {
        $localeContext->getLocale()->willReturn('en');
        $this->setLocaleContext($localeContext);

        $translatableFields = array('foo');

        $this->setTranslatableFields($translatableFields);

        $criteria = array(
            'baz' => array('foo', 'bar'),
        );

        foreach ($criteria as $property => $value) {
            $queryBuilder
                ->field($property)
                ->shouldBeCalled()
                ->willReturn($queryBuilder)
            ;

            $queryBuilder
                ->in($value)
                ->shouldBeCalled()
                ->willReturn($queryBuilder)
            ;
        }

        $this->findBy($criteria)->shouldReturn(null);
    }

    function it_returns_null_if_there_are_no_resources()
    {
        $this->findAll()->shouldReturn(null);
    }

    function it_creates_Pagerfanta_paginator()
    {
        $this
            ->createPaginator()
            ->shouldHaveType('Pagerfanta\Pagerfanta')
        ;
    }
}
