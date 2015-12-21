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
use Sylius\Component\Taxonomy\Model\TaxonomyTranslationInterface;

/**
 * @author Gonzalo Vilaseca <gvilaseca@reiss.co.uk>
 */
class TaxonomyTranslationSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Taxonomy\Model\TaxonomyTranslation');
    }

    function it_implements_Sylius_taxonomy_interface()
    {
        $this->shouldImplement(TaxonomyTranslationInterface::class);
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
}
