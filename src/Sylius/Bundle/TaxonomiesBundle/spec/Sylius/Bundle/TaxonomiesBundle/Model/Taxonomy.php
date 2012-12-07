<?php

namespace spec\Sylius\Bundle\TaxonomiesBundle\Model;

use PHPSpec2\ObjectBehavior;

/**
 * Taxonomy spec.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class Taxonomy extends ObjectBehavior
{
    function it_should_be_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\TaxonomiesBundle\Model\Taxonomy');
    }

    function it_should_be_Sylius_taxonomy()
    {
        $this->shouldImplement('Sylius\Bundle\TaxonomiesBundle\Model\TaxonomyInterface');
    }

    function it_should_have_no_id_by_default()
    {
        $this->getId()->shouldReturn(null);
    }

    function it_should_not_have_root_by_defualt()
    {
        $this->getRoot()->shouldReturn(null);
    }

    /**
     * @param Sylius\Bundle\TaxonomiesBundle\Model\TaxonInterface $taxon
     */
    function it_should_allow_definining_root_taxon($taxon)
    {
        $this->setRoot($taxon);
        $this->getRoot()->shouldReturn($taxon);
    }

    /**
     * @param Sylius\Bundle\TaxonomiesBundle\Model\TaxonInterface $taxon
     */
    function it_should_get_name_from_root_taxon($taxon)
    {
        $taxon->getName()->willReturn('Brand');
        $this->setRoot($taxon);

        $this->getName()->shouldReturn('Brand');
    }

    /**
     * @param Sylius\Bundle\TaxonomiesBundle\Model\TaxonInterface $taxon
     */
    function it_should_set_name_on_root_taxon($taxon)
    {
        $taxon->setName('Category')->shouldBeCalled();
        $this->setRoot($taxon);

        $this->setName('Category');
    }

    function it_should_intitialize_taxon_collection_by_defualt()
    {
        $this->getTaxons()->shouldHaveType('Doctrine\Common\Collections\Collection');
    }

    /**
     * @param Doctrine\Common\Collections\Collection $taxons
     */
    function its_taxon_collection_should_be_mutable($taxons)
    {
        $this->setTaxons($taxons);
        $this->getTaxons()->shouldReturn($taxons);
    }

    /**
     * @param Sylius\Bundle\TaxonomiesBundle\Model\TaxonInterface $taxon
     */
    function it_should_not_contain_taxon_unless_added($taxon)
    {
        $this->hasTaxon($taxon)->shouldReturn(false);
    }

    /**
     * @param Sylius\Bundle\TaxonomiesBundle\Model\TaxonInterface $taxon
     */
    function it_should_contain_taxon_when_added($taxon)
    {
        $this->addTaxon($taxon);
        $this->hasTaxon($taxon)->shouldReturn(true);
    }

    /**
     * @param Sylius\Bundle\TaxonomiesBundle\Model\TaxonInterface $taxon
     */
    function it_should_not_contain_taxon_after_removing($taxon)
    {
        $this->addTaxon($taxon);
        $this->hasTaxon($taxon)->shouldReturn(true);

        $this->removeTaxon($taxon);
        $this->hasTaxon($taxon)->shouldReturn(false);
    }

    /**
     * @param Sylius\Bundle\TaxonomiesBundle\Model\TaxonInterface $taxon
     */
    function it_should_assign_itself_to_taxon_when_adding($taxon)
    {
        $taxon->setTaxonomy($this)->shouldBeCalled();

        $this->addTaxon($taxon);
    }

    /**
     * @param Sylius\Bundle\TaxonomiesBundle\Model\TaxonInterface $taxon
     */
    function it_should_detach_itself_from_taxon_when_removing($taxon)
    {
        $taxon->setTaxonomy($this)->shouldBeCalled();
        $this->addTaxon($taxon);

        $taxon->setTaxonomy(null)->shouldBeCalled();
        $this->removeTaxon($taxon);
    }
}
