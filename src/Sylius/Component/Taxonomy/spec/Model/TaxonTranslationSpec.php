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

/**
 * @author Gonzalo Vilaseca <gvilaseca@reiss.co.uk>
 */
class TaxonTranslationSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Taxonomy\Model\TaxonTranslation');
    }

    function it_implements_Sylius_taxon_interface()
    {
        $this->shouldImplement('Sylius\Component\Taxonomy\Model\TaxonTranslationInterface');
    }

    function it_has_no_id_by_default()
    {
        $this->getId()->shouldReturn(null);
    }

    function it_is_unnamed_by_default()
    {
        $this->getName()->shouldReturn(null);
    }

    function its_name_is_mutable()
    {
        $this->setName('Brand');
        $this->getName()->shouldReturn('Brand');
    }

    function it_returns_name_when_converted_to_string()
    {
        $this->setName('Brand');
        $this->__toString()->shouldReturn('Brand');
    }

    function it_has_no_description_by_default()
    {
        $this->getDescription()->shouldReturn(null);
    }

    function its_description_is_mutable()
    {
        $this->setDescription('This is a list of brands.');
        $this->getDescription()->shouldReturn('This is a list of brands.');
    }

    function it_has_no_slug_by_default()
    {
        $this->getSlug()->shouldReturn(null);
    }

    function its_slug_is_mutable()
    {
        $this->setSlug('t-shirts');
        $this->getSlug()->shouldReturn('t-shirts');
    }

    function it_has_no_permalink_by_default()
    {
        $this->getPermalink()->shouldReturn(null);
    }

    function its_permalink_is_mutable()
    {
        $this->setPermalink('woman-clothing');
        $this->getPermalink()->shouldReturn('woman-clothing');
    }
}
