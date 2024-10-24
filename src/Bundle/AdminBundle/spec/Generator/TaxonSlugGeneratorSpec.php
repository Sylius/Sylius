<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace spec\Sylius\Bundle\AdminBundle\Generator;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Component\Core\Model\TaxonInterface;
use Sylius\Component\Taxonomy\Generator\TaxonSlugGeneratorInterface as BaseTaxonSlugGeneratorInterface;

final class TaxonSlugGeneratorSpec extends ObjectBehavior
{
    function let(BaseTaxonSlugGeneratorInterface $slugGenerator): void
    {
        $this->beConstructedWith($slugGenerator);
    }

    function it_generates_slug_for_taxon_name(BaseTaxonSlugGeneratorInterface $slugGenerator): void
    {
        $slugGenerator->generate(Argument::any(), 'pl_PL')->shouldNotBeCalled();

        $this->generate('Board games', 'pl_PL')->shouldReturn('board-games');
    }

    function it_generates_slug_for_taxon_name_replacing_apostrophes_with_hyphens(
        BaseTaxonSlugGeneratorInterface $slugGenerator,
    ): void {
        $slugGenerator->generate(Argument::any(), 'pl_PL')->shouldNotBeCalled();

        $this->generate('Rock\'n\'roll', 'pl_PL')->shouldReturn('rock-n-roll');
    }

    function it_generates_slug_for_taxon_name_and_its_parent(
        BaseTaxonSlugGeneratorInterface $slugGenerator,
        TaxonInterface $taxon,
    ): void {
        $slugGenerator->generate($taxon, 'pl_PL')->willReturn('games');

        $this->generate('Board games', 'pl_PL', $taxon)->shouldReturn('games/board-games');
    }
}
