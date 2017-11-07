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

namespace spec\Sylius\Bundle\ResourceBundle\Grid\Renderer;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\ResourceBundle\Controller\RequestConfiguration;
use Sylius\Bundle\ResourceBundle\Grid\Parser\OptionsParserInterface;
use Sylius\Bundle\ResourceBundle\Grid\View\ResourceGridView;
use Sylius\Component\Grid\Definition\Action;
use Sylius\Component\Grid\Renderer\BulkActionGridRendererInterface;
use Sylius\Component\Grid\Renderer\GridRendererInterface;
use Symfony\Component\HttpFoundation\Request;

final class TwigBulkActionGridRendererSpec extends ObjectBehavior
{
    function let(\Twig_Environment $twig, OptionsParserInterface $optionsParser): void
    {
        $this->beConstructedWith(
            $twig,
            $optionsParser,
            ['delete' => 'SyliusGridBundle:BulkAction:_delete.html.twig']
        );
    }

    function it_is_a_bulk_action_grid_renderer(): void
    {
        $this->shouldImplement(BulkActionGridRendererInterface::class);
    }

    function it_uses_twig_to_render_the_bulk_action(
        \Twig_Environment $twig,
        OptionsParserInterface $optionsParser,
        ResourceGridView $gridView,
        Action $bulkAction,
        RequestConfiguration $requestConfiguration,
        Request $request
    ): void {
        $bulkAction->getType()->willReturn('delete');
        $bulkAction->getOptions()->willReturn([]);

        $gridView->getRequestConfiguration()->willReturn($requestConfiguration);
        $requestConfiguration->getRequest()->willReturn($request);

        $optionsParser->parseOptions([], $request, null)->shouldBeCalled();

        $twig
            ->render('SyliusGridBundle:BulkAction:_delete.html.twig', [
                'grid' => $gridView,
                'action' => $bulkAction,
                'data' => null,
                'options' => [],
            ])
            ->willReturn('<a href="#">Delete</a>')
        ;

        $this->renderBulkAction($gridView, $bulkAction)->shouldReturn('<a href="#">Delete</a>');
    }

    function it_throws_an_exception_if_template_is_not_configured_for_given_bulk_action_type(
        ResourceGridView $gridView,
        Action $bulkAction
    ): void {
        $bulkAction->getType()->willReturn('foo');

        $this
            ->shouldThrow(new \InvalidArgumentException('Missing template for bulk action type "foo".'))
            ->during('renderBulkAction', [$gridView, $bulkAction])
        ;
    }
}
