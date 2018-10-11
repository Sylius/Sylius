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

namespace spec\Sylius\Bundle\GridBundle\Renderer;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Grid\Definition\Action;
use Sylius\Component\Grid\Renderer\BulkActionGridRendererInterface;
use Sylius\Component\Grid\View\GridViewInterface;

final class TwigBulkActionGridRendererSpec extends ObjectBehavior
{
    function let(\Twig_Environment $twig): void
    {
        $this->beConstructedWith($twig, ['delete' => '@SyliusGrid/BulkAction/_delete.html.twig']);
    }

    function it_is_a_bulk_action_grid_renderer(): void
    {
        $this->shouldImplement(BulkActionGridRendererInterface::class);
    }

    function it_uses_twig_to_render_the_bulk_action(
        \Twig_Environment $twig,
        GridViewInterface $gridView,
        Action $bulkAction
    ): void {
        $bulkAction->getType()->willReturn('delete');
        $bulkAction->getOptions()->willReturn([]);

        $twig
            ->render('@SyliusGrid/BulkAction/_delete.html.twig', [
                'grid' => $gridView,
                'action' => $bulkAction,
                'data' => null,
            ])
            ->willReturn('<a href="#">Delete</a>')
        ;

        $this->renderBulkAction($gridView, $bulkAction)->shouldReturn('<a href="#">Delete</a>');
    }

    function it_throws_an_exception_if_template_is_not_configured_for_given_bulk_action_type(
        GridViewInterface $gridView,
        Action $bulkAction
    ): void {
        $bulkAction->getType()->willReturn('foo');

        $this
            ->shouldThrow(new \InvalidArgumentException('Missing template for bulk action type "foo".'))
            ->during('renderBulkAction', [$gridView, $bulkAction])
        ;
    }
}
