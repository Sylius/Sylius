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
use Sylius\Component\Taxonomy\Model\TaxonTranslation;
use Sylius\Component\Taxonomy\Model\TaxonTranslationInterface;

/**
 * @author Gonzalo Vilaseca <gvilaseca@reiss.co.uk>
 */
final class TaxonTranslationSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(TaxonTranslation::class);
    }

    function it_implements_taxon_translation_interface()
    {
        $this->shouldImplement(TaxonTranslationInterface::class);
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
}
