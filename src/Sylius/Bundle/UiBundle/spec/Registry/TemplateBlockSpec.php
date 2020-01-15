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
        $this->beConstructedWith('block_name', 'block.html.twig', ['foo' => 'bar'], 10, false);

        $this->getName()->shouldReturn('block_name');
        $this->getTemplate()->shouldReturn('block.html.twig');
        $this->getContext()->shouldReturn(['foo' => 'bar']);
        $this->getPriority()->shouldReturn(10);
        $this->isEnabled()->shouldReturn(false);
    }
}
