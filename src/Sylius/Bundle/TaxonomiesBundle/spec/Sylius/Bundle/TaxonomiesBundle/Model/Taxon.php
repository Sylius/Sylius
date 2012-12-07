<?php

namespace spec\Sylius\Bundle\TaxonomiesBundle\Model;

use PHPSpec2\ObjectBehavior;

/**
 * Taxon spec.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class Taxon extends ObjectBehavior
{
    function it_should_be_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\TaxonomiesBundle\Model\Taxon');
    }

    function it_should_be_Sylius_taxon()
    {
        $this->shouldImplement('Sylius\Bundle\TaxonomiesBundle\Model\TaxonInterface');
    }

    function it_should_have_no_id_by_default()
    {
        $this->getId()->shouldReturn(null);
    }

    function it_should_not_belong_to_any_taxonomy_by_default()
    {
        $this->getTaxonomy()->shouldReturn(null);
    }

    /**
     * @param Sylius\Bundle\TaxonomiesBundle\Model\TaxonomyInterface $taxonomy
     */
    function it_should_allow_assigning_itself_to_taxonomy($taxonomy)
    {
        $this->setTaxonomy($taxonomy);
        $this->getTaxonomy()->shouldReturn($taxonomy);
    }

    /**
     * @param Sylius\Bundle\TaxonomiesBundle\Model\TaxonomyInterface $taxonomy
     */
    function it_should_allow_detaching_taxonomy($taxonomy)
    {
        $this->setTaxonomy($taxonomy);
        $this->getTaxonomy()->shouldReturn($taxonomy);

        $this->setTaxonomy(null);
        $this->getTaxonomy()->shouldReturn(null);
    }

    function it_should_have_no_parent_by_default()
    {
        $this->getParent()->shouldReturn(null);
    }

    /**
     * @param Sylius\Bundle\TaxonomiesBundle\Model\TaxonInterface $taxon
     */
    function its_parent_should_be_mutable($taxon)
    {
        $this->setParent($taxon);
        $this->getParent()->shouldReturn($taxon);
    }

    function it_should_be_unnamed_by_default()
    {
        $this->getName()->shouldReturn(null);
    }

    function its_name_should_be_mutable()
    {
        $this->setName('Brand');
        $this->getName()->shouldReturn('Brand');
    }

    function it_should_not_have_slug_by_default()
    {
        $this->getSlug()->shouldReturn(null);
    }

    function its_slug_should_be_mutable()
    {
        $this->setSlug('t-shirts');
        $this->getSlug()->shouldReturn('t-shirts');
    }

    function it_should_not_have_permalink_by_default()
    {
        $this->getPermalink()->shouldReturn(null);
    }

    function its_permalink_should_be_mutable()
    {
        $this->setPermalink('woman-clothing');
        $this->getPermalink()->shouldReturn('woman-clothing');
    }

    /**
     * @param Sylius\Bundle\TaxonomiesBundle\Model\TaxonInterface $taxonA
     * @param Sylius\Bundle\TaxonomiesBundle\Model\TaxonInterface $taxonB
     */
    function it_should_generate_a_permalink_by_linking_its_slug_and_parent_taxon_slugs($taxonA, $taxonB)
    {
        $taxonB->getParent()->willReturn($taxonA);

        $taxonA->getSlug()->willReturn('clothing');
        $taxonB->getSlug()->willReturn('accessories');

        $this->setSlug('scarves-and-shawls');
        $this->setParent($taxonA);

        $this->getPermalink()->shouldReturn('clothing/accessories/scarves-and-shawls');
    }

    /**
     * @param Sylius\Bundle\TaxonomiesBundle\Model\TaxonInterface $taxonA
     * @param Sylius\Bundle\TaxonomiesBundle\Model\TaxonInterface $taxonB
     */
    function it_should_generate_a_permalink_only_when_its_null($taxonA, $taxonB)
    {
        $this->setPermalink('super-promotion-slug');

        $taxonB->getParent()->willReturn($taxonA);

        $taxonA->getSlug()->willReturn('clothing');
        $taxonB->getSlug()->willReturn('accessories');

        $this->setSlug('scarves-and-shawls');
        $this->setParent($taxonA);

        $this->getPermalink()->shouldReturn('super-promotion-slug');
    }
}
