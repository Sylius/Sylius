<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Grid\Provider;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Grid\Definition\ArrayToDefinitionConverterInterface;
use Sylius\Component\Grid\Definition\Grid;
use Sylius\Component\Grid\Provider\ArrayGridProvider;
use Sylius\Component\Grid\Provider\GridProviderInterface;
use Sylius\Component\Grid\Provider\UndefinedGridException;

/**
 * @mixin ArrayGridProvider
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
final class ArrayGridProviderSpec extends ObjectBehavior
{
    function let(ArrayToDefinitionConverterInterface $converter, Grid $firstGrid, Grid $secondGrid, Grid $thirdGrid)
    {
        $converter->convert('sylius_admin_tax_category', ['configuration1'])->willReturn($firstGrid);
        $converter->convert('sylius_admin_product', ['configuration2'])->willReturn($secondGrid);
        $converter->convert('sylius_admin_order', ['configuration3'])->willReturn($thirdGrid);

        $this->beConstructedWith($converter, [
            'sylius_admin_tax_category' => ['configuration1'],
            'sylius_admin_product' => ['configuration2'],
            'sylius_admin_order' => ['configuration3'],
        ]);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(ArrayGridProvider::class);
    }

    function it_implements_grid_provider_interface()
    {
        $this->shouldImplement(GridProviderInterface::class);
    }

    function it_returns_grid_definition_by_name(Grid $firstGrid, Grid $secondGrid, Grid $thirdGrid)
    {
        $this->get('sylius_admin_tax_category')->shouldReturn($firstGrid);
        $this->get('sylius_admin_product')->shouldReturn($secondGrid);
        $this->get('sylius_admin_order')->shouldReturn($thirdGrid);
    }

    function it_throws_an_exception_if_grid_does_not_exist()
    {
        $this
            ->shouldThrow(new UndefinedGridException('sylius_admin_order_item'))
            ->during('get', ['sylius_admin_order_item'])
        ;
    }
}
