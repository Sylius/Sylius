<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Core\Model;

use PhpSpec\ObjectBehavior;

class TaxonSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Core\Model\Taxon');
    }

    public function it_is_Sylius_Taxon()
    {
        $this->shouldImplement('Sylius\Component\Taxonomy\Model\TaxonInterface');
        $this->shouldImplement('Sylius\Component\Core\Model\ImageInterface');
    }

    public function it_should_not_path_defined_by_default()
    {
        $this->getPath()->shouldReturn(null);
    }
}
