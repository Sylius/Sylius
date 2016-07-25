<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Core\Promotion\Filter;

use Doctrine\Common\Collections\Collection;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\TaxonInterface;
use Sylius\Component\Core\Promotion\Filter\FilterInterface;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
final class TaxonFilterSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Core\Promotion\Filter\TaxonFilter');
    }

    function it_implements_filter_interface()
    {
        $this->shouldImplement(FilterInterface::class);
    }

    function it_filters_passed_order_items_with_given_configuration(
        OrderItemInterface $item1,
        OrderItemInterface $item2,
        ProductInterface $product1,
        ProductInterface $product2,
        TaxonInterface $taxon1,
        TaxonInterface $taxon2
    ) {
        $item1->getProduct()->willReturn($product1);
        $product1->getTaxons()->willReturn([$taxon1]);
        $taxon1->getCode()->willReturn('taxon1');

        $item2->getProduct()->willReturn($product2);
        $product2->getTaxons()->willReturn([$taxon2]);
        $taxon2->getCode()->willReturn('taxon2');

        $this->filter([$item1, $item2], ['filters' => ['taxons' => ['taxon1']]])->shouldReturn([$item1]);
    }

    function it_returns_all_items_if_configuration_is_invalid(OrderItemInterface $item)
    {
        $this->filter([$item], [])->shouldReturn([$item]);
    }
}
