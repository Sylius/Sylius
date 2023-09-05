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

namespace spec\Sylius\Component\Taxonomy\Positioner;

use Doctrine\Persistence\ObjectManager;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Taxonomy\Model\TaxonInterface;
use Sylius\Component\Taxonomy\Repository\TaxonRepositoryInterface;

final class TaxonPositionerSpec extends ObjectBehavior
{
    public function let(
        TaxonRepositoryInterface $taxonRepository,
        ObjectManager $taxonManager,
    ): void {
        $this->beConstructedWith($taxonRepository, $taxonManager);
    }

    public function it_moves_taxon_up(
        TaxonRepositoryInterface $taxonRepository,
        ObjectManager $taxonManager,
        TaxonInterface $taxonToBeMoved,
        TaxonInterface $taxonAbove,
    ): void {
        $taxonRepository->findOneAbove($taxonToBeMoved)->willReturn($taxonAbove);

        $taxonToBeMoved->getPosition()->willReturn(1);
        $taxonAbove->getPosition()->willReturn(0);

        $taxonToBeMoved->setPosition(0)->shouldBeCalled();
        $taxonAbove->setPosition(1)->shouldBeCalled();

        $this->moveUp($taxonToBeMoved);
    }

    public function it_should_do_nothing_when_taxon_above_does_not_exist(
        TaxonRepositoryInterface $taxonRepository,
        ObjectManager $taxonManager,
        TaxonInterface $taxonToBeMoved,
    ): void {
        $taxonRepository->findOneAbove($taxonToBeMoved)->willReturn(null);

        $taxonToBeMoved->getPosition()->shouldNotBeCalled();

        $this->moveUp($taxonToBeMoved);
    }

    public function it_moves_taxon_down(
        TaxonRepositoryInterface $taxonRepository,
        ObjectManager $taxonManager,
        TaxonInterface $taxonToBeMoved,
        TaxonInterface $taxonBelow,
    ): void {
        $taxonRepository->findOneBelow($taxonToBeMoved)->willReturn($taxonBelow);

        $taxonToBeMoved->getPosition()->willReturn(0);
        $taxonBelow->getPosition()->willReturn(1);

        $taxonToBeMoved->setPosition(1)->shouldBeCalled();
        $taxonBelow->setPosition(0)->shouldBeCalled();

        $this->moveDown($taxonToBeMoved);
    }

    public function it_should_do_nothing_when_taxon_below_does_not_exist(
        TaxonRepositoryInterface $taxonRepository,
        ObjectManager $taxonManager,
        TaxonInterface $taxonToBeMoved,
    ): void {
        $taxonRepository->findOneBelow($taxonToBeMoved)->willReturn(null);

        $taxonToBeMoved->getPosition()->shouldNotBeCalled();

        $this->moveDown($taxonToBeMoved);
    }
}
