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
use Sylius\Bundle\UiBundle\Registry\TemplateBlock;
use Sylius\Bundle\UiBundle\Registry\TemplateBlockRegistryInterface;

final class TemplateBlockRegistrySpec extends ObjectBehavior
{
    function let(): void
    {
        $this->beConstructedWith([]);
    }

    function it_is_a_template_block_registry(): void
    {
        $this->shouldImplement(TemplateBlockRegistryInterface::class);
    }

    function it_returns_all_template_blocks(): void
    {
        $templateBlock = new TemplateBlock('block_name', 'block.html.twig', 10, true);

        $this->beConstructedWith(['event' => [$templateBlock]]);

        $this->all()->shouldReturn(['event' => [$templateBlock]]);
    }

    function it_returns_enabled_template_blocks_for_a_given_event(): void
    {
        $firstTemplateBlock = new TemplateBlock('first_block', 'first.html.twig', 0, true);
        $secondTemplateBlock = new TemplateBlock('second_block', 'second.html.twig', 10, false);
        $thirdTemplateBlock = new TemplateBlock('third_block', 'third.html.twig', 50, true);

        $this->beConstructedWith(['event' => [$firstTemplateBlock, $secondTemplateBlock], 'another_event' => [$thirdTemplateBlock]]);

        $this->findEnabledForEvent('event')->shouldReturn([$firstTemplateBlock]);
    }
}
