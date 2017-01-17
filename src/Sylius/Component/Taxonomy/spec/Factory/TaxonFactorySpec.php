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
use Sylius\Component\Taxonomy\Factory\TaxonFactory;
use Sylius\Component\Taxonomy\Factory\TaxonFactoryInterface;
use Sylius\Component\Taxonomy\Model\TaxonInterface;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
final class TaxonFactorySpec extends ObjectBehavior
{
    function let(FactoryInterface $factory)
    {
        $this->beConstructedWith($factory);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(TaxonFactory::class);
    }

    function it_implements_taxon_factory_interface()
    {
        $this->shouldImplement(TaxonFactoryInterface::class);
    }

    function it_uses_decorated_factory_to_create_new_taxon(
        FactoryInterface $factory,
        TaxonInterface $taxon
    ) {
        $factory->createNew()->willReturn($taxon);

        $this->createNew()->shouldReturn($taxon);
    }

    function it_creates_taxon_for_given_parent_taxon(
        FactoryInterface $factory,
        TaxonInterface $parent,
        TaxonInterface $taxon
    ) {
        $factory->createNew()->willReturn($taxon);
        $taxon->setParent($parent)->shouldBeCalled();

        $this->createForParent($parent)->shouldReturn($taxon);
    }
}
