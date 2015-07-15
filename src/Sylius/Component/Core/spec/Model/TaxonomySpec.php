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

class TaxonomySpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Core\Model\Taxonomy');
    }

    public function it_is_Sylius_Taxonomy()
    {
        $this->shouldImplement('Sylius\Component\Taxonomy\Model\TaxonomyInterface');
    }

    public function it_should_not_path_defined_by_default()
    {
        $this->getRoot()->getPath()->shouldReturn(null);
    }
}
