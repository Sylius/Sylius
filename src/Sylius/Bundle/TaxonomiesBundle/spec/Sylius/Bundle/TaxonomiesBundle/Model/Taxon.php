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
}
