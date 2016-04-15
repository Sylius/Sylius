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
use Prophecy\Argument;
use Sylius\Bundle\GridBundle\Templating\Helper\GridHelper;
use Sylius\Component\Grid\Definition\Action;
use Sylius\Component\Grid\Definition\Field;
use Sylius\Component\Grid\Definition\Grid;
use Sylius\Component\Grid\Parameters;
use Sylius\Component\Grid\Renderer\GridRendererInterface;
use Sylius\Component\Grid\View\GridView;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Templating\Helper\Helper;
use Symfony\Component\Templating\Helper\HelperInterface;

/**
 * @mixin GridHelper
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class GridHelperSpec extends ObjectBehavior
{
    function let(GridRendererInterface $gridRenderer)
    {
        $this->beConstructedWith($gridRenderer);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\GridBundle\Templating\Helper\GridHelper');
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

    function it_adds_proper_sorting_parameter_to_path(GridView $gridView, Grid $grid, Field $field, Parameters $parameters)
    {
        $gridView->getParameters()->willReturn($parameters);
        $gridView->getDefinition()->willReturn($grid);
        
        $grid->getSorting()->willReturn([]);

        $field->getName()->willReturn('nameAndDescription');
        $field->getSortingPath()->willReturn('name');
        
        $parameters->has('sorting')->willReturn(true);
        $parameters->get('sorting')->willReturn(['name' => 'asc']);
        $parameters->get('criteria', [])->willReturn(['code' => ['type' => 'contains', 'value' => 'vat']]);

        $this
            ->applySorting('/tax-rates/', $gridView, $field)
            ->shouldReturn('/tax-rates/?sorting%5Bname%5D=desc&criteria%5Bcode%5D%5Btype%5D=contains&criteria%5Bcode%5D%5Bvalue%5D=vat')
        ;
    }

    function it_has_name()
    {
        $this->getName()->shouldReturn('sylius_grid');
    }
}
