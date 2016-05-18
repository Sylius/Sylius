<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\UserBundle\Doctrine\ORM;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\QueryBuilder;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;

class GroupRepositorySpec extends ObjectBehavior
{
    public function let(EntityManager $em, ClassMetadata $classMetadata)
    {
        $this->beConstructedWith($em, $classMetadata);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\UserBundle\Doctrine\ORM\GroupRepository');
    }

    function it_is_a_repository()
    {
        $this->shouldHaveType(EntityRepository::class);
    }

    function it_has_a_form_query_builder($em, QueryBuilder $builder)
    {
        $em->createQueryBuilder()->shouldBeCalled()->willReturn($builder);
        $builder->select('o')->shouldBeCalled()->willReturn($builder);
        $builder->from(Argument::any(), 'o', Argument::cetera())->shouldBeCalled();

        $this->getFormQueryBuilder();
    }
}
