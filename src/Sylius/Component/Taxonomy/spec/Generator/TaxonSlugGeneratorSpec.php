<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace spec\Sylius\Component\Taxonomy\Generator;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Taxonomy\Generator\TaxonSlugGeneratorInterface;
use Sylius\Component\Taxonomy\Model\TaxonInterface;
use Sylius\Component\Taxonomy\Model\TaxonTranslationInterface;

final class TaxonSlugGeneratorSpec extends ObjectBehavior
{
    function it_implements_taxon_slug_generator_interface(): void
    {
        $this->shouldImplement(TaxonSlugGeneratorInterface::class);
    }

    function it_generates_slug_for_root_taxon(
        TaxonInterface $taxon,
        TaxonTranslationInterface $taxonTranslation
    ): void {
        $taxon->getTranslation('pl_PL')->willReturn($taxonTranslation);
        $taxonTranslation->getName()->willReturn('Board games');

        $taxon->getParent()->willReturn(null);

        $this->generate($taxon, 'pl_PL')->shouldReturn('board-games');
    }

    function it_generates_slug_for_root_taxon_replacing_apostrophes_with_hyphens(
        TaxonInterface $taxon,
        TaxonTranslationInterface $taxonTranslation
    ): void {
        $taxon->getTranslation('pl_PL')->willReturn($taxonTranslation);
        $taxonTranslation->getName()->willReturn('Rock\'n\'roll');

        $taxon->getParent()->willReturn(null);

        $this->generate($taxon, 'pl_PL')->shouldReturn('rock-n-roll');
    }

    function it_generates_slug_for_child_taxon_when_parent_taxon_already_has_slug(
        TaxonInterface $taxon,
        TaxonTranslationInterface $taxonTranslation,
        TaxonInterface $parentTaxon,
        TaxonTranslationInterface $parentTaxonTranslation
    ): void {
        $taxon->getTranslation('pl_PL')->willReturn($taxonTranslation);
        $taxonTranslation->getName()->willReturn('Battle games');

        $taxon->getParent()->willReturn($parentTaxon);

        $parentTaxon->getTranslation('pl_PL')->willReturn($parentTaxonTranslation);
        $parentTaxonTranslation->getSlug()->willReturn('board-games');

        $this->generate($taxon, 'pl_PL')->shouldReturn('board-games/battle-games');
    }

    function it_generates_slug_for_child_taxon_even_when_parent_taxon_does_not_have_slug(
        TaxonInterface $taxon,
        TaxonTranslationInterface $taxonTranslation,
        TaxonInterface $parentTaxon,
        TaxonTranslationInterface $parentTaxonTranslation
    ): void {
        $taxon->getTranslation('pl_PL')->willReturn($taxonTranslation);
        $taxonTranslation->getName()->willReturn('Battle games');

        $taxon->getParent()->willReturn($parentTaxon);

        $parentTaxon->getTranslation('pl_PL')->willReturn($parentTaxonTranslation);
        $parentTaxonTranslation->getSlug()->willReturn(null);
        $parentTaxonTranslation->getName()->willReturn('Board games');

        $parentTaxon->getParent()->willReturn(null);

        $this->generate($taxon, 'pl_PL')->shouldReturn('board-games/battle-games');
    }

    function it_throws_an_exception_if_passed_taxon_has_no_name(
        TaxonInterface $taxon,
        TaxonTranslationInterface $taxonTranslation
    ): void {
        $taxon->getTranslation('pl_PL')->willReturn($taxonTranslation);
        $taxonTranslation->getName()->willReturn('');

        $taxon->getParent()->willReturn(null);

        $this->shouldThrow(\InvalidArgumentException::class)->during('generate', [$taxon, 'pl_PL']);
    }
}
