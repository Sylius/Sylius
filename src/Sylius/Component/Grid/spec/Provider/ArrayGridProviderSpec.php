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
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
final class ArrayGridProviderSpec extends ObjectBehavior
{
    function let(ArrayToDefinitionConverterInterface $converter, Grid $firstGrid, Grid $secondGrid, Grid $thirdGrid, Grid $fourthGrid)
    {
        $converter->convert('sylius_admin_tax_category', ['configuration1'])->willReturn($firstGrid);
        $converter->convert('sylius_admin_product', ['configuration2' => 'foo'])->willReturn($secondGrid);
        $converter->convert('sylius_admin_order', ['configuration3'])->willReturn($thirdGrid);
        $converter->convert('sylius_admin_product_from_taxon', ['configuration4' => 'bar', 'configuration2' => 'foo'])->willReturn($fourthGrid);

        $this->beConstructedWith($converter, [
            'sylius_admin_tax_category' => ['configuration1'],
            'sylius_admin_product' => ['configuration2' => 'foo'],
            'sylius_admin_order' => ['configuration3'],
            'sylius_admin_product_from_taxon' => ['extends' => 'sylius_admin_product', 'configuration4' => 'bar'],
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

    function it_returns_cloned_grid_definition_by_name(Grid $firstGrid, Grid $secondGrid, Grid $thirdGrid)
    {
        $this->get('sylius_admin_tax_category')->shouldBeLike($firstGrid);
        $this->get('sylius_admin_product')->shouldBeLike($secondGrid);
        $this->get('sylius_admin_order')->shouldBeLike($thirdGrid);
    }

    function it_supports_grid_inheritance(Grid $fourthGrid)
    {
        $this->get('sylius_admin_product_from_taxon')->shouldBeLike($fourthGrid);
    }

    function it_throws_an_exception_if_grid_does_not_exist()
    {
        $this
            ->shouldThrow(new UndefinedGridException('sylius_admin_order_item'))
            ->during('get', ['sylius_admin_order_item'])
        ;
    }
}
