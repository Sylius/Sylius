<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\CoreBundle\Form\Type;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\TaxonomyBundle\Form\Type\TaxonType;
use Symfony\Component\Form\FormTypeInterface;

class TaxonTypeSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith('Taxon', []);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\CoreBundle\Form\Type\TaxonType');
    }

    function it_should_be_a_form_type()
    {
        $this->shouldImplement(FormTypeInterface::class);
    }

    function it_should_extend_Sylius_taxon_base_form_type()
    {
        $this->shouldHaveType(TaxonType::class);
    }
}
