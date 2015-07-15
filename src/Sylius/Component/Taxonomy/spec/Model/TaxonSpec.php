<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Taxonomy\Model;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Taxonomy\Model\TaxonInterface;
use Sylius\Component\Taxonomy\Model\TaxonomyInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 * @author Gonzalo Vilaseca <gvilaseca@reiss.co.uk>
 */
class TaxonSpec extends ObjectBehavior
{
    public function let()
    {
        $this->setCurrentLocale('en_US');
        $this->setFallbackLocale('en_US');
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Taxonomy\Model\Taxon');
    }

    public function it_implements_Sylius_taxon_interface()
    {
        $this->shouldImplement('Sylius\Component\Taxonomy\Model\TaxonInterface');
    }

    public function it_has_no_id_by_default()
    {
        $this->getId()->shouldReturn(null);
    }

    public function it_does_not_belong_to_taxonomy_by_default()
    {
        $this->getTaxonomy()->shouldReturn(null);
    }

    public function it_allows_assigning_itself_to_taxonomy(TaxonomyInterface $taxonomy, TaxonInterface $root)
    {
        $taxonomy->getRoot()->willReturn($root);

        $this->setTaxonomy($taxonomy);
        $this->getTaxonomy()->shouldReturn($taxonomy);
    }

    public function it_allows_detaching_itself_from_taxonomy(TaxonomyInterface $taxonomy, TaxonInterface $root)
    {
        $taxonomy->getRoot()->willReturn($root);

        $this->setTaxonomy($taxonomy);
        $this->getTaxonomy()->shouldReturn($taxonomy);

        $this->setTaxonomy(null);
        $this->getTaxonomy()->shouldReturn(null);
    }

    public function it_has_no_parent_by_default()
    {
        $this->getParent()->shouldReturn(null);
    }

    public function its_parent_is_mutable(TaxonInterface $taxon)
    {
        $this->setParent($taxon);
        $this->getParent()->shouldReturn($taxon);
    }

    public function it_is_root_by_default()
    {
        $this->shouldBeRoot();
    }

    public function it_is_not_root_when_has_parent(TaxonInterface $taxon)
    {
        $this->setParent($taxon);
        $this->shouldNotBeRoot();
    }

    public function it_is_root_when_has_no_parent(TaxonInterface $taxon)
    {
        $this->shouldBeRoot();

        $this->setParent($taxon);
        $this->shouldNotBeRoot();
        $this->setParent(null);

        $this->shouldBeRoot();
    }

    public function it_is_unnamed_by_default()
    {
        $this->getName()->shouldReturn(null);
    }

    public function its_name_is_mutable()
    {
        $this->setName('Brand');
        $this->getName()->shouldReturn('Brand');
    }

    public function it_returns_name_when_converted_to_string()
    {
        $this->setName('T-Shirt material');
        $this->__toString()->shouldReturn('T-Shirt material');
    }

    public function it_has_no_description_by_default()
    {
        $this->getDescription()->shouldReturn(null);
    }

    public function its_description_is_mutable()
    {
        $this->setDescription('This is a list of brands.');
        $this->getDescription()->shouldReturn('This is a list of brands.');
    }

    public function it_has_no_slug_by_default()
    {
        $this->getSlug()->shouldReturn(null);
    }

    public function its_slug_is_mutable()
    {
        $this->setSlug('t-shirts');
        $this->getSlug()->shouldReturn('t-shirts');
    }

    public function it_has_no_permalink_by_default()
    {
        $this->getPermalink()->shouldReturn(null);
    }

    public function its_permalink_is_mutable()
    {
        $this->setPermalink('woman-clothing');
        $this->getPermalink()->shouldReturn('woman-clothing');
    }

    public function it_initializes_child_taxon_collection_by_default()
    {
        $this->getChildren()->shouldHaveType('Doctrine\Common\Collections\Collection');
    }

    public function it_allows_to_check_if_given_taxon_is_its_child(TaxonInterface $taxon)
    {
        $this->hasChild($taxon)->shouldReturn(false);
    }

    public function it_allows_to_add_child_taxons(TaxonomyInterface $taxonomy, TaxonInterface $taxon)
    {
        $this->setTaxonomy($taxonomy);

        $taxon->setTaxonomy($taxonomy)->shouldBeCalled();
        $taxon->setParent($this)->shouldBeCalled();

        $this->addChild($taxon);
    }

    public function it_allows_to_remove_child_taxons(TaxonomyInterface $taxonomy, TaxonInterface $taxon)
    {
        $this->setTaxonomy($taxonomy);

        $taxon->setTaxonomy($taxonomy)->shouldBeCalled();
        $taxon->setParent($this)->shouldBeCalled();

        $this->addChild($taxon);

        $taxon->setTaxonomy(null)->shouldBeCalled();
        $taxon->setParent(null)->shouldBeCalled();

        $this->removeChild($taxon);
    }
}
