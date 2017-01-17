<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Taxonomy\Generator;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Taxonomy\Generator\TaxonSlugGenerator;
use Sylius\Component\Taxonomy\Generator\TaxonSlugGeneratorInterface;
use Sylius\Component\Taxonomy\Model\TaxonInterface;
use Sylius\Component\Taxonomy\Repository\TaxonRepositoryInterface;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
final class TaxonSlugGeneratorSpec extends ObjectBehavior
{
    function let(TaxonRepositoryInterface $taxonRepository)
    {
        $this->beConstructedWith($taxonRepository);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(TaxonSlugGenerator::class);
    }

    function it_implements_taxon_slug_generator_interface()
    {
        $this->shouldImplement(TaxonSlugGeneratorInterface::class);
    }

    function it_generates_slug_based_on_new_taxon_name_and_parent_taxon_slug(
        TaxonInterface $parent,
        TaxonRepositoryInterface $taxonRepository
    ) {
        $taxonRepository->find(1)->willReturn($parent);
        $parent->getSlug()->willReturn('board-games');

        $this->generate('Battle games', 1)->shouldReturn('board-games/battle-games');;
    }

    function it_generates_slug_based_on_new_taxon_name_if_this_taxon_has_no_parent()
    {
        $this->generate('Board games')->shouldReturn('board-games');;
    }

    function it_throws_exception_if_parent_taxon_with_given_id_does_not_exist(
        TaxonRepositoryInterface $taxonRepository
    ) {
        $taxonRepository->find(1)->willReturn(null);

        $this
            ->shouldThrow(new \InvalidArgumentException('There is no parent taxon with id 1.'))
            ->during('generate', ['Battle games', 1])
        ;
    }
}
