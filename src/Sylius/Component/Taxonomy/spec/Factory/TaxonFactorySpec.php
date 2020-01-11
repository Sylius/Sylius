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

namespace spec\Sylius\Component\Taxonomy\Factory;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Taxonomy\Factory\TaxonFactoryInterface;
use Sylius\Component\Taxonomy\Model\TaxonInterface;

final class TaxonFactorySpec extends ObjectBehavior
{
    function let(FactoryInterface $factory): void
    {
        $this->beConstructedWith($factory);
    }

    function it_implements_taxon_factory_interface(): void
    {
        $this->shouldImplement(TaxonFactoryInterface::class);
    }

    function it_uses_decorated_factory_to_create_new_taxon(
        FactoryInterface $factory,
        TaxonInterface $taxon
    ): void {
        $factory->createNew()->willReturn($taxon);

        $this->createNew()->shouldReturn($taxon);
    }

    function it_creates_taxon_for_given_parent_taxon(
        FactoryInterface $factory,
        TaxonInterface $parent,
        TaxonInterface $taxon
    ): void {
        $factory->createNew()->willReturn($taxon);
        $taxon->setParent($parent)->shouldBeCalled();

        $this->createForParent($parent)->shouldReturn($taxon);
    }
}
