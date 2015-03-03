<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\ResourceBundle\Doctrine\ORM;

use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Query\Expr;
use Doctrine\ORM\QueryBuilder;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Component\Locale\Context\LocaleContextInterface;

require_once __DIR__.'/../../Fixture/Entity/TranslatableFoo.php';

/**
 * Doctrine ORM driver translatable entity repository spec.
 *
 * @author Gonzalo Vilaseca <gvilaseca@reiss.co.uk>
 */
class TranslatableEntityRepositorySpec extends ObjectBehavior
{
    function let(EntityManager $entityManager, ClassMetadata $class, QueryBuilder $queryBuilder, AbstractQuery $query)
    {
        $class->name = 'spec\Sylius\Bundle\ResourceBundle\Fixture\Entity\TranslatableFoo';

        $entityManager
            ->createQueryBuilder()
            ->willReturn($queryBuilder)
        ;

        $queryBuilder
            ->select(Argument::any())
            ->willReturn($queryBuilder)
        ;

        $queryBuilder
            ->from(Argument::any(), Argument::any())
            ->willReturn($queryBuilder)
        ;

        $queryBuilder
            ->addSelect(Argument::any())
            ->willReturn($queryBuilder)
        ;

        $queryBuilder
            ->leftJoin(Argument::any(), Argument::any())
            ->willReturn($queryBuilder)
        ;

        $queryBuilder
            ->getQuery()
            ->willReturn($query)
        ;

        $this->beConstructedWith($entityManager, $class);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\ResourceBundle\Doctrine\ORM\TranslatableEntityRepository');
    }

    function it_implements_Sylius_translatable_repository_interface()
    {
        $this->shouldImplement('Sylius\Bundle\ResourceBundle\Doctrine\TranslatableEntityRepositoryInterface');
    }

    function it_sets_current_locale_on_created_object(LocaleContextInterface $localeContext)
    {
        $localeContext->getLocale()->willReturn('en');

        $this->setLocaleContext($localeContext);

        $this->createNew()->getCurrentLocale()->shouldReturn('en');
    }

    function it_applies_criteria_when_finding_one($queryBuilder, Expr $expr)
    {
        $translatableFields = array('foo');

        $this->setTranslatableFields($translatableFields);

        $criteria = array(
            'foo' => 'bar',
            'bar' => 'baz',
        );

        foreach ($criteria as $property => $value) {
            $queryBuilder
                ->expr()
                ->shouldBeCalled()
                ->willReturn($expr)
            ;

            if (in_array($property, $translatableFields)) {
                $expr
                    ->eq('translation.'.$property, ':translation_'.$property)
                    ->shouldBeCalled()
                    ->willReturn('o.'.$property.' = :'.$value)
                ;

                $queryBuilder
                    ->setParameter('translation_'.$property, $value)
                    ->shouldBeCalled()
                    ->willReturn($queryBuilder)
                ;
            }else{
                $expr
                    ->eq('o.'.$property, ':'.$property)
                    ->shouldBeCalled()
                    ->willReturn('o.'.$property.' = :'.$value)
                ;

                $queryBuilder
                    ->setParameter($property, $value)
                    ->shouldBeCalled()
                    ->willReturn($queryBuilder)
                ;
            }

            $queryBuilder
                ->andWhere('o.'.$property.' = :'.$value)
                ->shouldBeCalled()
                ->willReturn($queryBuilder)
            ;

            $queryBuilder
                ->addSelect('translation')
                ->shouldBeCalled()
                ->willReturn($queryBuilder)
            ;

            $queryBuilder
                ->leftJoin('o.translations', 'translation')
                ->shouldBeCalled()
                ->willReturn($queryBuilder)
            ;
        }

        $this->findOneBy($criteria)->shouldReturn(null);
    }

    function it_applies_criteria_when_finding_by($queryBuilder, Expr $expr)
    {
        $translatableFields = array('foo');

        $this->setTranslatableFields($translatableFields);

        $criteria = array(
            'foo' => 'bar',
            'bar' => 'baz',
        );

        foreach ($criteria as $property => $value) {
            $queryBuilder
                ->expr()
                ->shouldBeCalled()
                ->willReturn($expr)
            ;

            if (in_array($property, $translatableFields)) {
                $expr
                    ->eq('translation.'.$property, ':translation_'.$property)
                    ->shouldBeCalled()
                    ->willReturn('o.'.$property.' = :'.$value)
                ;

                $queryBuilder
                    ->setParameter('translation_'.$property, $value)
                    ->shouldBeCalled()
                    ->willReturn($queryBuilder)
                ;
            }else{
                $expr
                    ->eq('o.'.$property, ':'.$property)
                    ->shouldBeCalled()
                    ->willReturn('o.'.$property.' = :'.$value)
                ;

                $queryBuilder
                    ->setParameter($property, $value)
                    ->shouldBeCalled()
                    ->willReturn($queryBuilder)
                ;
            }

            $queryBuilder
                ->andWhere('o.'.$property.' = :'.$value)
                ->shouldBeCalled()
                ->willReturn($queryBuilder)
            ;

            $queryBuilder
                ->addSelect('translation')
                ->shouldBeCalled()
                ->willReturn($queryBuilder)
            ;

            $queryBuilder
                ->leftJoin('o.translations', 'translation')
                ->shouldBeCalled()
                ->willReturn($queryBuilder)
            ;
        }

        $this->findBy($criteria)->shouldReturn(null);
    }

    function it_applies_criteria_when_finding_by_array($queryBuilder, Expr $expr)
    {
        $translatableFields = array('foo');

        $this->setTranslatableFields($translatableFields);

        $criteria = array(
            'baz' => array('foo', 'bar'),
        );

        foreach ($criteria as $property => $value) {
            $queryBuilder
                ->expr()
                ->shouldBeCalled()
                ->willReturn($expr)
            ;

            $expr
                ->in('o.'.$property, $value)
                ->shouldBeCalled()
                ->willReturn('o.'.$property.' IN (:'.$property.')')
            ;

            $queryBuilder
                ->andWhere('o.'.$property.' IN (:'.$property.')')
                ->shouldBeCalled()
                ->willReturn($queryBuilder)
            ;

            $queryBuilder
                ->addSelect('translation')
                ->shouldBeCalled()
                ->willReturn($queryBuilder)
            ;

            $queryBuilder
                ->leftJoin('o.translations', 'translation')
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

    public function it_has_fluent_interface(LocaleContextInterface $localeContext)
    {
        $this->setLocaleContext($localeContext)->shouldReturn($this);
        $this->setTranslatableFields(array('en'))->shouldReturn($this);
    }
}
