<?php

namespace spec\Sylius\Bundle\TaxonomiesBundle\Entity;

use PHPSpec2\ObjectBehavior;

/**
 * Taxon entity spec.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class Taxon extends ObjectBehavior
{
    function it_should_be_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\TaxonomiesBundle\Entity\Taxon');
    }

    function it_should_be_Sylius_taxon()
    {
        $this->shouldImplement('Sylius\Bundle\TaxonomiesBundle\Model\TaxonInterface');
    }

    function it_should_extend_Sylius_taxon_model()
    {
        $this->shouldHaveType('Sylius\Bundle\TaxonomiesBundle\Model\Taxon');
    }
}
