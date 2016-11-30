<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\GridBundle\Templating\Helper;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\GridBundle\Templating\Helper\GridHelper;
use Sylius\Component\Grid\Definition\Action;
use Sylius\Component\Grid\Definition\Field;
use Sylius\Component\Grid\Renderer\GridRendererInterface;
use Sylius\Component\Grid\View\GridView;
use Symfony\Component\Templating\Helper\Helper;
use Symfony\Component\Templating\Helper\HelperInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
final class GridHelperSpec extends ObjectBehavior
{
    function let(GridRendererInterface $gridRenderer)
    {
        $this->beConstructedWith($gridRenderer);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(GridHelper::class);
    }

    function it_is_a_Symfony_Templating_helper()
    {
        $this->shouldImplement(HelperInterface::class);
    }

    function it_extends_base_Symfony_Templating_helper()
    {
        $this->shouldHaveType(Helper::class);
    }

    function it_uses_grid_renderer_to_render_grid(GridRendererInterface $gridRenderer, GridView $gridView)
    {
        $gridRenderer->render($gridView, null)->willReturn('<html>Grid!</html>');
        $this->renderGrid($gridView, null)->shouldReturn('<html>Grid!</html>');
    }

    function it_uses_grid_renderer_to_render_field(GridRendererInterface $gridRenderer, GridView $gridView, Field $field)
    {
        $gridRenderer->renderField($gridView, $field, 'foo')->willReturn('Value');
        $this->renderField($gridView, $field, 'foo')->shouldReturn('Value');
    }

    function it_uses_grid_renderer_to_render_action(GridRendererInterface $gridRenderer, GridView $gridView, Action $action)
    {
        $gridRenderer->renderAction($gridView, $action, null)->willReturn('<a href="#">Go go Gadget arms!</a>');
        $this->renderAction($gridView, $action)->shouldReturn('<a href="#">Go go Gadget arms!</a>');
    }

    function it_has_name()
    {
        $this->getName()->shouldReturn('sylius_grid');
    }
}
