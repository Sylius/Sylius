<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\TranslationBundle\Doctrine\ORM;

use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Query\Expr;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Component\Translation\Provider\LocaleProviderInterface;

require_once __DIR__.'/../../../../ResourceBundle/spec/Fixture/Entity/TranslatableFoo.php';

/**
 * Doctrine ORM driver translatable entity repository spec.
 *
 * @author Gonzalo Vilaseca <gvilaseca@reiss.co.uk>
 */
class TranslatableResourceRepositorySpec extends ObjectBehavior
{
    public function let(EntityManager $entityManager, ClassMetadata $class, QueryBuilder $queryBuilder, AbstractQuery $query)
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
            ->from(Argument::any(), Argument::any(), Argument::cetera())
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

    public function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\TranslationBundle\Doctrine\ORM\TranslatableResourceRepository');
    }

    public function it_implements_Sylius_translatable_repository_interface()
    {
        $this->shouldImplement('Sylius\Component\Translation\Repository\TranslatableResourceRepositoryInterface');
    }

    public function it_sets_current_locale_on_created_object(LocaleProviderInterface $localeProvider)
    {
        $localeProvider->getCurrentLocale()->willReturn('en_US');
        $localeProvider->getFallbackLocale()->willReturn('en_US');

        $this->setLocaleProvider($localeProvider);

        $this->createNew()->getCurrentLocale()->shouldReturn('en_US');
        $this->createNew()->getFallbackLocale()->shouldReturn('en_US');
    }

    public function it_applies_criteria_when_finding_one($queryBuilder, Expr $expr)
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
                    ->eq('translation.'.$property, ':'.$property)
                    ->shouldBeCalled()
                    ->willReturn('o.'.$property.' = :'.$value)
                ;

                $queryBuilder
                    ->setParameter($property, $value)
                    ->shouldBeCalled()
                    ->willReturn($queryBuilder)
                ;
            } else {
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

    public function it_applies_criteria_when_finding_by($queryBuilder, Expr $expr)
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
                    ->eq('translation.'.$property, ':'.$property)
                    ->shouldBeCalled()
                    ->willReturn('o.'.$property.' = :'.$value)
                ;

                $queryBuilder
                    ->setParameter($property, $value)
                    ->shouldBeCalled()
                    ->willReturn($queryBuilder)
                ;
            } else {
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

    public function it_applies_criteria_when_finding_by_array($queryBuilder, Expr $expr)
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

    public function it_returns_null_if_there_are_no_resources()
    {
        $this->findAll()->shouldReturn(null);
    }

    public function it_creates_Pagerfanta_paginator()
    {
        $this
            ->createPaginator()
            ->shouldHaveType('Pagerfanta\Pagerfanta')
        ;
    }

    public function it_has_fluent_interface(LocaleProviderInterface $localeProvider)
    {
        $this->setLocaleProvider($localeProvider)->shouldReturn($this);
        $this->setTranslatableFields(array('name'))->shouldReturn($this);
    }
}
