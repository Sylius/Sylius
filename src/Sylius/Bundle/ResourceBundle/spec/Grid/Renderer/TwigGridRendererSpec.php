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
use Sylius\Component\Grid\Renderer\GridRendererInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * @author Grzegorz Sadowski <grzegorz.sadowski@lakion.com>
 */
final class TwigGridRendererSpec extends ObjectBehavior
{
    function let(
        GridRendererInterface $gridRenderer,
        \Twig_Environment $twig,
        OptionsParserInterface $optionsParser
    ): void {
        $actionTemplates = [
            'link' => 'SyliusGridBundle:Action:_link.html.twig',
            'form' => 'SyliusGridBundle:Action:_form.html.twig',
        ];

        $this->beConstructedWith(
            $gridRenderer,
            $twig,
            $optionsParser,
            $actionTemplates
        );
    }

    function it_is_a_grid_renderer(): void
    {
        $this->shouldImplement(GridRendererInterface::class);
    }

    function it_uses_twig_to_render_the_action(
        \Twig_Environment $twig,
        OptionsParserInterface $optionsParser,
        ResourceGridView $gridView,
        Action $action,
        RequestConfiguration $requestConfiguration,
        Request $request
    ): void {
        $action->getType()->willReturn('link');
        $action->getOptions()->willReturn([]);

        $gridView->getRequestConfiguration()->willReturn($requestConfiguration);
        $requestConfiguration->getRequest()->willReturn($request);

        $optionsParser->parseOptions([], $request, null)->shouldBeCalled();

        $twig
            ->render('SyliusGridBundle:Action:_link.html.twig', [
                'grid' => $gridView,
                'action' => $action,
                'data' => null,
                'options' => [],
            ])
            ->willReturn('<a href="#">Action!</a>')
        ;

        $this->renderAction($gridView, $action)->shouldReturn('<a href="#">Action!</a>');
    }

    function it_throws_an_exception_if_template_is_not_configured_for_given_action_type(
        ResourceGridView $gridView,
        Action $action
    ): void {
        $action->getType()->willReturn('foo');

        $this
            ->shouldThrow(new \InvalidArgumentException('Missing template for action type "foo".'))
            ->during('renderAction', [$gridView, $action])
        ;
    }
}
