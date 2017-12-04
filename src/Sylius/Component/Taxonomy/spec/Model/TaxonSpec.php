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

namespace spec\Sylius\Component\Taxonomy\Model;

use Doctrine\Common\Collections\Collection;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Taxonomy\Model\TaxonInterface;

final class TaxonSpec extends ObjectBehavior
{
    public function let()
    {
        $this->setCurrentLocale('en_US');
        $this->setFallbackLocale('en_US');
    }

    function it_implements_taxon_interface(): void
    {
        $this->shouldImplement(TaxonInterface::class);
    }

    function it_has_no_id_by_default(): void
    {
        $this->getId()->shouldReturn(null);
    }

    function its_code_is_mutable(): void
    {
        $this->setCode('TX2');
        $this->getCode()->shouldReturn('TX2');
    }

    function it_has_no_parent_by_default(): void
    {
        $this->getParent()->shouldReturn(null);
    }

    function its_parent_is_mutable(TaxonInterface $taxon): void
    {
        $this->setParent($taxon);
        $this->getParent()->shouldReturn($taxon);
    }

    function it_is_root_by_default(): void
    {
        $this->shouldBeRoot();
    }

    function it_is_not_root_when_has_parent(TaxonInterface $taxon): void
    {
        $this->setParent($taxon);
        $this->shouldNotBeRoot();
    }

    function it_is_root_when_has_no_parent(TaxonInterface $taxon): void
    {
        $this->shouldBeRoot();

        $this->setParent($taxon);
        $this->shouldNotBeRoot();
        $this->setParent(null);

        $this->shouldBeRoot();
    }

    function it_returns_a_list_of_ancestors(
        TaxonInterface $categoryTaxon,
        TaxonInterface $tshirtsTaxon
    ): void {
        $tshirtsTaxon->getParent()->willReturn($categoryTaxon);

        $tshirtsTaxon->addChild($this)->shouldBeCalled();
        $this->setParent($tshirtsTaxon);

        $this->getAncestors()->shouldIterateAs([$tshirtsTaxon->getWrappedObject(), $categoryTaxon->getWrappedObject()]);
    }

    function it_returns_a_list_with_single_ancestor(TaxonInterface $parentTaxon): void
    {
        $parentTaxon->getParent()->willReturn(null);
        $parentTaxon->addChild($this)->shouldBeCalled();
        $this->setParent($parentTaxon);

        $this->getAncestors()->shouldIterateAs([$parentTaxon->getWrappedObject()]);
    }

    function it_returns_an_empty_list_of_ancestors_if_called_on_root_taxon(): void
    {
        $this->getAncestors()->shouldIterateAs([]);
    }

    function it_is_unnamed_by_default(): void
    {
        $this->getName()->shouldReturn(null);
    }

    function its_name_is_mutable(): void
    {
        $this->setName('Brand');
        $this->getName()->shouldReturn('Brand');
    }

    function it_returns_name_when_converted_to_string(): void
    {
        $this->setName('T-Shirt material');
        $this->__toString()->shouldReturn('T-Shirt material');
    }

    function it_has_no_description_by_default(): void
    {
        $this->getDescription()->shouldReturn(null);
    }

    function its_description_is_mutable(): void
    {
        $this->setDescription('This is a list of brands.');
        $this->getDescription()->shouldReturn('This is a list of brands.');
    }

    function it_has_no_slug_by_default(): void
    {
        $this->getSlug()->shouldReturn(null);
    }

    function its_slug_is_mutable(): void
    {
        $this->setSlug('t-shirts');
        $this->getSlug()->shouldReturn('t-shirts');
    }

    function it_initializes_child_taxon_collection_by_default(): void
    {
        $this->getChildren()->shouldHaveType(Collection::class);
    }

    function it_allows_to_check_if_given_taxon_is_its_child(TaxonInterface $taxon): void
    {
        $this->hasChild($taxon)->shouldReturn(false);
    }

    function it_allows_to_add_child_taxons(TaxonInterface $taxon): void
    {
        $taxon->getParent()->willReturn(null);
        $taxon->setParent($this)->shouldBeCalled();

        $this->addChild($taxon);
    }

    function it_allows_to_remove_child_taxons(TaxonInterface $taxon): void
    {
        $taxon->getParent()->willReturn(null);
        $taxon->setParent($this)->shouldBeCalled();

        $this->addChild($taxon);

        $taxon->setParent(null)->shouldBeCalled();

        $this->removeChild($taxon);
    }

    function it_has_position(): void
    {
        $this->setPosition(0);
        $this->getPosition()->shouldReturn(0);
    }

    function it_has_not_children_by_default(): void
    {
        $this->hasChildren()->shouldReturn(false);
    }

    function it_has_children_when_you_have_added_child(TaxonInterface $taxon): void
    {
        $this->addChild($taxon);

        $this->hasChildren()->shouldReturn(true);
    }
}
