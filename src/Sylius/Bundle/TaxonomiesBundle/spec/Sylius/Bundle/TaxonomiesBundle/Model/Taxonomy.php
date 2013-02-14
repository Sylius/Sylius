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
    function it_should_not_have_name_by_default($taxon)
    {
        $taxon->setName('Brand')->shouldBeCalled();
        $this->setRoot($taxon);

        $this->setName('Brand');
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
}
