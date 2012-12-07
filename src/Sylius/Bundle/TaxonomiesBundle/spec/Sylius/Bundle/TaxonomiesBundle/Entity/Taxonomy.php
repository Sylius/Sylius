<?php

namespace spec\Sylius\Bundle\TaxonomiesBundle\Entity;

use PHPSpec2\ObjectBehavior;

/**
 * Taxonomy entity spec.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class Taxonomy extends ObjectBehavior
{
    function it_should_be_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\TaxonomiesBundle\Entity\Taxonomy');
    }

    function it_should_be_Sylius_taxonomy()
    {
        $this->shouldImplement('Sylius\Bundle\TaxonomiesBundle\Model\TaxonomyInterface');
    }

    function it_should_extend_Sylius_taxonomy_model()
    {
        $this->shouldHaveType('Sylius\Bundle\TaxonomiesBundle\Model\Taxonomy');
    }
}
