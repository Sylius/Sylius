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
use Sylius\Component\Taxonomy\Model\TaxonInterface;

class TaxonSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Core\Model\Taxon');
    }

    function it_is_Sylius_Taxon()
    {
        $this->shouldImplement(TaxonInterface::class);
    }
}
