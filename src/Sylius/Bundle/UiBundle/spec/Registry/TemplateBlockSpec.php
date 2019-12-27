<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace spec\Sylius\Bundle\UiBundle\Registry;

use PhpSpec\ObjectBehavior;

final class TemplateBlockSpec extends ObjectBehavior
{
    function it_represents_a_template_block(): void
    {
        $this->beConstructedWith('block_name', 'block.html.twig', 10, false);

        $this->name()->shouldReturn('block_name');
        $this->template()->shouldReturn('block.html.twig');
        $this->priority()->shouldReturn(10);
        $this->enabled()->shouldReturn(false);
    }
}
