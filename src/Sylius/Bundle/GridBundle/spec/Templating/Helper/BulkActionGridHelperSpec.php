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

namespace spec\Sylius\Bundle\GridBundle\Templating\Helper;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Grid\Definition\Action;
use Sylius\Component\Grid\Definition\Field;
use Sylius\Component\Grid\Renderer\BulkActionGridRendererInterface;
use Sylius\Component\Grid\Renderer\GridRendererInterface;
use Sylius\Component\Grid\View\GridView;
use Symfony\Component\Templating\Helper\Helper;
use Symfony\Component\Templating\Helper\HelperInterface;

final class BulkActionGridHelperSpec extends ObjectBehavior
{
    function let(BulkActionGridRendererInterface $bulkActionGridRenderer): void
    {
        $this->beConstructedWith($bulkActionGridRenderer);
    }

    function it_is_a_templating_helper(): void
    {
        $this->shouldImplement(HelperInterface::class);
    }

    function it_extends_base_templating_helper(): void
    {
        $this->shouldHaveType(Helper::class);
    }

    function it_uses_a_grid_renderer_to_render_a_bulk_action(
        BulkActionGridRendererInterface $bulkActionGridRenderer,
        GridView $gridView,
        Action $bulkAction
    ): void {
        $bulkActionGridRenderer->renderBulkAction($gridView, $bulkAction, null)->willReturn('<a href="#">Delete</a>');
        $this->renderBulkAction($gridView, $bulkAction)->shouldReturn('<a href="#">Delete</a>');
    }

    function it_has_name(): void
    {
        $this->getName()->shouldReturn('sylius_bulk_action_grid');
    }
}
