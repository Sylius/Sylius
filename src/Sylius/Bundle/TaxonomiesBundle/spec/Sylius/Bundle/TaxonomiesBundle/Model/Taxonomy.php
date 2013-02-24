<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\TaxonomiesBundle\Model;

use PHPSpec2\ObjectBehavior;

/**
 * Taxonomy spec.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class Taxonomy extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\TaxonomiesBundle\Model\Taxonomy');
    }

    function it_implements_Sylius_taxonomy_interface()
    {
        $this->shouldImplement('Sylius\Bundle\TaxonomiesBundle\Model\TaxonomyInterface');
    }

    function it_has_no_id_by_default()
    {
        $this->getId()->shouldReturn(null);
    }

    function it_has_no_root_by_default()
    {
        $this->getRoot()->shouldReturn(null);
    }

    /**
     * @param Sylius\Bundle\TaxonomiesBundle\Model\TaxonInterface $taxon
     */
    function it_allows_setting_the_root_taxon($taxon)
    {
        $this->setRoot($taxon);
        $this->getRoot()->shouldReturn($taxon);
    }

    function it_is_unnamed_by_default()
    {
        $this->getName()->shouldReturn(null);
    }

    /**
     * @param Sylius\Bundle\TaxonomiesBundle\Model\TaxonInterface $taxon
     */
    function its_name_is_mutable($taxon)
    {
        $taxon->setName('Brand')->shouldBeCalled();
        $this->setRoot($taxon);

        $this->setName('Brand');
        $this->getName()->shouldReturn('Brand');
    }

    /**
     * @param Sylius\Bundle\TaxonomiesBundle\Model\TaxonInterface $taxon
     */
    function it_also_sets_name_on_the_root_taxon($taxon)
    {
        $taxon->setName('Category')->shouldBeCalled();
        $this->setRoot($taxon);

        $this->setName('Category');
    }

    /**
     * @param Sylius\Bundle\TaxonomiesBundle\Model\TaxonInterface $root
     * @param Sylius\Bundle\TaxonomiesBundle\Model\TaxonInterface $taxon
     */
    function it_delegates_the_hasTaxon_method_to_root_taxon($root, $taxon)
    {
        $this->setRoot($root);

        $root->hasChild($taxon)->willReturn(true);
        $this->hasTaxon($taxon)->shouldReturn(true);

        $root->hasChild($taxon)->willReturn(false);
        $this->hasTaxon($taxon)->shouldReturn(false);
    }

    /**
     * @param Sylius\Bundle\TaxonomiesBundle\Model\TaxonInterface $root
     * @param Sylius\Bundle\TaxonomiesBundle\Model\TaxonInterface $taxon
     */
    function it_delegates_addTaxon_method_to_root_taxon($root, $taxon)
    {
        $this->setRoot($root);

        $root->addChild($taxon)->shouldBeCalled();
        $this->addTaxon($taxon);
    }

    /**
     * @param Sylius\Bundle\TaxonomiesBundle\Model\TaxonInterface $root
     * @param Sylius\Bundle\TaxonomiesBundle\Model\TaxonInterface $taxon
     */
    function it_delegates_removeTaxon_method_to_root_taxon($root, $taxon)
    {
        $this->setRoot($root);

        $root->removeChild($taxon)->shouldBeCalled();
        $this->removeTaxon($taxon);
    }
}
