<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\RbacBundle\Doctrine\ORM;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Component\Rbac\Model\PermissionInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Query\Expr;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\QueryBuilder;

/**
 * @author Arnaud Langlade <arn0d.dev@gmail.com>
 */
class PermissionRepositorySpec extends ObjectBehavior
{
    public function let(EntityManager $em, ClassMetadata $classMetadata)
    {
        $this->beConstructedWith($em, $classMetadata);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\RbacBundle\Doctrine\ORM\PermissionRepository');
    }

    public function it_gets_chield_permission(
        $em,
        $builder,
        PermissionInterface $permission,
        QueryBuilder $builder,
        AbstractQuery $query,
        Expr $expr
    ) {
        $permission->getRight()->shouldBeCalled()->willReturn(1);
        $permission->getLeft()->shouldBeCalled()->willReturn(2);

        $builder->expr()->shouldBeCalled()->willReturn($expr);
        $expr->lt('o.left', 1)->shouldBeCalled()->willReturn($expr);
        $expr->gt('o.left', 2)->shouldBeCalled()->willReturn($expr);

        $em->createQueryBuilder()->shouldBeCalled()->willReturn($builder);
        $builder->select('o')->shouldBeCalled()->willReturn($builder);
        $builder->from(Argument::any(), 'o')->shouldBeCalled()->willReturn($builder);
        $builder->where(Argument::type('Doctrine\ORM\Query\Expr'))->shouldBeCalled()->willReturn($builder);
        $builder->andWhere(Argument::type('Doctrine\ORM\Query\Expr'))->shouldBeCalled()->willReturn($builder);

        $builder->getQuery()->shouldBeCalled()->willReturn($query);
        $query->execute()->shouldBeCalled();

        $this->getChildPermissions($permission);
    }
}
