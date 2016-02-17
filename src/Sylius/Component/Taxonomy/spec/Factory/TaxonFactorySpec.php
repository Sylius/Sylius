<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Taxonomy\Factory;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\Component\Taxonomy\Factory\TaxonFactoryInterface;
use Sylius\Component\Taxonomy\Model\TaxonInterface;
use Sylius\Component\Taxonomy\Model\TaxonomyInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class TaxonFactorySpec extends ObjectBehavior
{
    function let(FactoryInterface $factory, RepositoryInterface $taxonomyRepository)
    {
        $this->beConstructedWith($factory, $taxonomyRepository);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Taxonomy\Factory\TaxonFactory');
    }

    function it_is_a_resource_factory()
    {
        $this->shouldImplement(FactoryInterface::class);
    }

    function it_implements_taxon_factory_interface()
    {
        $this->shouldImplement(TaxonFactoryInterface::class);
    }

    function it_creates_new_taxon(FactoryInterface $factory, TaxonInterface $taxon)
    {
        $factory->createNew()->willReturn($taxon);

        $this->createNew()->shouldReturn($taxon);
    }

    function it_throws_an_exception_when_taxonomy_is_not_found(RepositoryInterface $taxonomyRepository)
    {
        $taxonomyRepository->find(15)->willReturn(null);

        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('createForTaxonomy', [15])
        ;
    }

    function it_creates_a_taxon_and_assigns_a_taxonomy_to_id(
        FactoryInterface $factory,
        RepositoryInterface $taxonomyRepository,
        TaxonomyInterface $taxonomy,
        TaxonInterface $taxon
    ) {
        $factory->createNew()->willReturn($taxon);
        $taxonomyRepository->find(13)->willReturn($taxonomy);
        $taxon->setTaxonomy($taxonomy)->shouldBeCalled();

        $this->createForTaxonomy(13)->shouldReturn($taxon);
    }
}
