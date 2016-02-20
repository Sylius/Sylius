<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\GridBundle\Twig;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\GridBundle\Templating\Helper\GridHelper;
use Sylius\Bundle\GridBundle\Twig\GridExtension;
use Sylius\Component\Grid\Definition\Action;
use Sylius\Component\Grid\Definition\Field;
use Sylius\Component\Grid\View\GridView;

/**
 * @mixin GridExtension
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class GridExtensionSpec extends ObjectBehavior
{
    function let(GridHelper $gridHelper)
    {
        $this->beConstructedWith($gridHelper);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\GridBundle\Twig\GridExtension');
    }

    function it_is_a_Twig_extension()
    {
        $this->shouldHaveType(\Twig_Extension::class);
    }

    function it_defines_functions()
    {
        $this->getFunctions()->shouldHaveCount(3);
    }

    function it_delegates_grid_rendering_to_the_helper(GridHelper $gridHelper, GridView $gridView)
    {
        $gridHelper->renderGrid($gridView)->willReturn('<html>Grid!</html>');
        
        $this->renderGrid($gridView)->shouldReturn('<html>Grid!</html>');
    }

    function it_delegates_field_rendering_to_the_helper(GridHelper $gridHelper, GridView $gridView, Field $field)
    {
        $gridHelper->renderField($gridView, $field, 'foo')->willReturn('Value');

        $this->renderField($gridView, $field, 'foo')->shouldReturn('Value');
    }

    function it_delegates_action_rendering_to_the_helper(GridHelper $gridHelper, GridView $gridView, Action $action)
    {
        $gridHelper->renderAction($gridView, $action)->willReturn('<a href="#">Greet!</a>');

        $this->renderAction($gridView, $action)->shouldReturn('<a href="#">Greet!</a>');
    }

    function it_has_name()
    {
        $this->getName()->shouldReturn('sylius_grid');
    }
}
