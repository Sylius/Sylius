<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\ChannelBundle\Doctrine\ORM;

use Doctrine\ORM\Query;
use Doctrine\ORM\Query\Expr;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\QueryBuilder;

/**
 * @author Arnaud Langlade <arn0d.dev@gmail.com>
 */
class ChannelRepositorySpec extends ObjectBehavior
{
    function let(EntityManager $em, ClassMetadata $classMetadata)
    {
        $this->beConstructedWith($em, $classMetadata);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\ChannelBundle\Doctrine\ORM\ChannelRepository');
    }

    function it_is_arepository()
    {
        $this->shouldHaveType('Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository');
        $this->shouldImplement('Sylius\Component\Channel\Repository\ChannelRepositoryInterface');
    }

    function it_finds_by_host_name($em, QueryBuilder $builder, AbstractQuery $query, Expr $expr)
    {
        $em->createQueryBuilder()->shouldBeCalled()->willReturn($builder);
        $builder->expr()->shouldBeCalled()->willReturn($expr);
        $expr->like('o.url', ':hostname')->shouldBeCalled()->willReturn($expr);

        $builder->select('o')->shouldBeCalled()->willReturn($builder);
        $builder->from(Argument::any(), 'o')->shouldBeCalled()->willReturn($builder);
        $builder->andWhere($expr)->shouldBeCalled()->willReturn($builder);
        $builder->setParameter('hostname', '%host%')->shouldBeCalled()->willReturn($builder);

        $builder->getQuery()->shouldBeCalled()->willReturn($query);
        $query->getOneOrNullResult()->shouldBeCalled();

        $this->findMatchingHostname('host');
    }
}
