<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\CoreBundle\Model;

use PhpSpec\ObjectBehavior;

class TaxonSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\CoreBundle\Model\Taxon');
    }

    function it_is_Sylius_Taxon()
    {
        $this->shouldImplement('Sylius\Bundle\TaxonomiesBundle\Model\TaxonInterface');
        $this->shouldImplement('Sylius\Bundle\CoreBundle\Model\ImageInterface');
    }

    function it_should_not_path_defined_by_default()
    {
        $this->getPath()->shouldReturn(null);
    }
}
