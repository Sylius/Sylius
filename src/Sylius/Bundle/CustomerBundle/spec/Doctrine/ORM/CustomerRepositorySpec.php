<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\CustomerBundle\Doctrine\ORM;

use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Query\Expr;
use Doctrine\ORM\Query\FilterCollection;
use Doctrine\ORM\QueryBuilder;
use Pagerfanta\Pagerfanta;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\CustomerBundle\Doctrine\ORM\CustomerRepository;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Sylius\Component\Customer\Repository\CustomerRepositoryInterface;

/**
 * @author Anna Walasek <anna.walasek@lakion.com>
 */
final class CustomerRepositorySpec extends ObjectBehavior
{
    public function let(EntityManager $entityManager, ClassMetadata $classMetadata)
    {
        $this->beConstructedWith($entityManager, $classMetadata);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(CustomerRepository::class);
    }

    function it_is_a_repository()
    {
        $this->shouldHaveType(EntityRepository::class);
    }

    function it_implements_customer_repository_interface()
    {
        $this->shouldImplement(CustomerRepositoryInterface::class);
    }

    function it_count_customers(EntityManager $entityManager, QueryBuilder $builder, AbstractQuery $query)
    {
        $entityManager->createQueryBuilder()->shouldBeCalled()->willReturn($builder);
        $builder->from(Argument::any(), 'o', Argument::cetera())->shouldBeCalled()->willReturn($builder);
        $builder->select('o')->shouldBeCalled()->willReturn($builder);
        $builder->select('COUNT(o.id)')->shouldBeCalled()->willReturn($builder);
        $builder->getQuery()->shouldBeCalled()->willReturn($query);
        
        $query->getSingleScalarResult()->shouldBeCalled();

        $this->count();
    }
}
