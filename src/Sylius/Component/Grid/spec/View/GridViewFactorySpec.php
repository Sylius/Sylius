<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Grid\View;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Component\Grid\Data\DataProviderInterface;
use Sylius\Component\Grid\Definition\Grid;
use Sylius\Component\Grid\Parameters;
use Sylius\Component\Grid\View\GridView;
use Sylius\Component\Grid\View\GridViewFactory;
use Sylius\Component\Grid\View\GridViewFactoryInterface;

/**
 * @mixin GridViewFactory
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class GridViewFactorySpec extends ObjectBehavior
{
    function let(DataProviderInterface $dataProvider)
    {
        $this->beConstructedWith($dataProvider);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Grid\View\GridViewFactory');
    }

    function it_implements_grid_view_factory_interface()
    {
        $this->shouldImplement(GridViewFactoryInterface::class);
    }

    function it_uses_data_provider_to_create_a_view_with_data_and_definition(
        DataProviderInterface $dataProvider,
        Grid $grid,
        Parameters $parameters
    ) {
        $expectedGridView = new GridView(['foo', 'bar'], $grid->getWrappedObject(), $parameters->getWrappedObject());
        
        $dataProvider->getData($grid, $parameters)->willReturn(['foo', 'bar']);
        
        $this->create($grid, $parameters)->shouldBeSameGridViewAs($expectedGridView);
    }

    public function getMatchers()
    {
        return [
            'beSameGridViewAs' => function ($subject, $key) {
                return serialize($subject) === serialize($key);
            },
        ];
    }
}
