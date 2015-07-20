<?php

namespace spec\Sylius\BackendBundle\Repository;

use Doctrine\ORM\Query\FilterCollection;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\Query\Expr;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\EntityManager;

class ProductRepositorySpec extends ObjectBehavior
{
    public function let(EntityManager $em, ClassMetadata $classMetadata)
    {
        $this->beConstructedWith($em, $classMetadata);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\BackendBundle\Repository\ProductRepository');
    }

    public function it_is_repository()
    {
        $this->shouldHaveType('Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository');
    }

    /**
     * @param Doctrine\ORM\EntityManager $em
     * @param Doctrine\ORM\QueryBuilder $builder
     * @param Doctrine\ORM\AbstractQuery $query
     * @param Doctrine\ORM\Query\FilterCollection $filterCollection
     * @param Doctrine\ORM\Query\Expr $expr
     */
    public function it_count_products(
        $em,
        QueryBuilder $builder,
        AbstractQuery $query,
        FilterCollection $filterCollection,
        Expr $expr
    ) {
        $em->getFilters()->willReturn($filterCollection);
        $filterCollection->enable('softdeleteable');
        $em->createQueryBuilder()->shouldBeCalled()->willReturn($builder);


//        $em->createQueryBuilder()->willReturn($builder);
//        $filterCollection->enable('softdeleteable')->shouldBeCalled();

//        $builder->expr()->shouldBeCalled()->willReturn($expr);
//
//        $builder->select('COUNT(o.id)')->shouldBeCalled()->willReturn($builder);
//        $builder->from(Argument::any(), 'o')->shouldBeCalled()->willReturn($builder);

//        $expr->('o.expiresAt', ':now')->shouldBeCalled()->willReturn($expr);
//        $expr->eq('o.state', ':state')->shouldBeCalled()->willReturn($expr);
//
//        $builder->select('o')->shouldBeCalled()->willReturn($builder);
//        $builder->from(Argument::any(), 'o')->shouldBeCalled()->willReturn($builder);
//        $builder->leftJoin('o.items', 'item')->shouldBeCalled()->willReturn($builder);
//        $builder->addSelect('item')->shouldBeCalled()->willReturn($builder);
//        $builder->andWhere(Argument::any())->shouldBeCalled()->willReturn($builder);
//        $builder->andWhere(Argument::any())->shouldBeCalled()->willReturn($builder);
//        $builder->setParameter('now', Argument::type('\DateTime'))->shouldBeCalled()->willReturn($builder);
//        $builder->setParameter('state', OrderInterface::STATE_CART)->shouldBeCalled()->willReturn($builder);

//        $builder->getQuery()->shouldBeCalled()->willReturn($query);
//        $query->getSingleScalarResult()->shouldBeCalled();
//
        $this->countProducts(false);

    }
}