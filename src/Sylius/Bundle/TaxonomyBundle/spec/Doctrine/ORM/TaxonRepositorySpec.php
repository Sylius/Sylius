<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\TaxonomyBundle\Doctrine\ORM;

use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\QueryBuilder;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\EntityManager;
use Sylius\Component\Taxonomy\Model\TaxonomyInterface;

class TaxonRepositorySpec extends ObjectBehavior
{
    function let(EntityManager $em, ClassMetadata $classMetadata)
    {
        $this->beConstructedWith($em, $classMetadata);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\TaxonomyBundle\Doctrine\ORM\TaxonRepository');
    }

    function it_finds_taxon_as_a_list($em, TaxonomyInterface $taxonomy, QueryBuilder $builder, AbstractQuery $query)
    {
        $em->createQueryBuilder()->shouldBeCalled()->willReturn($builder);
        $builder->select('o')->shouldBeCalled()->willReturn($builder);
        $builder->from(Argument::any(), 'o')->shouldBeCalled()->willReturn($builder);
        $builder->addSelect('translation')->shouldBeCalled()->willReturn($builder);
        $builder->leftJoin('o.translations', 'translation')->shouldBeCalled()->willReturn($builder);
        $builder->where('o.taxonomy = :taxonomy')->shouldBeCalled()->willReturn($builder);
        $builder->andWhere('o.parent IS NOT NULL')->shouldBeCalled()->willReturn($builder);
        $builder->setParameter('taxonomy', $taxonomy)->shouldBeCalled()->willReturn($builder);
        $builder->orderBy('o.left')->shouldBeCalled()->willReturn($builder);

        $builder->getQuery()->shouldBeCalled()->willReturn($query);
        $query->getResult()->shouldBeCalled();

        $this->getTaxonsAsList($taxonomy);
    }

    function it_finds_one_taxon_by_permalink($em, QueryBuilder $builder, AbstractQuery $query)
    {
        $em->createQueryBuilder()->shouldBeCalled()->willReturn($builder);
        $builder->select('o')->shouldBeCalled()->willReturn($builder);
        $builder->from(Argument::any(), 'o')->shouldBeCalled()->willReturn($builder);
        $builder->addSelect('translation')->shouldBeCalled()->willReturn($builder);
        $builder->leftJoin('o.translations', 'translation')->shouldBeCalled()->willReturn($builder);
        $builder->where('translation.permalink = :permalink')->shouldBeCalled()->willReturn($builder);
        $builder->setParameter('permalink', 'link')->shouldBeCalled()->willReturn($builder);
        $builder->orderBy('o.left')->shouldBeCalled()->willReturn($builder);

        $builder->getQuery()->shouldBeCalled()->willReturn($query);
        $query->getOneOrNullResult()->shouldBeCalled();

        $this->findOneByPermalink('link');
    }
}
