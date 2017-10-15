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

namespace spec\Sylius\Component\Core\Model;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductTaxonInterface;
use Sylius\Component\Core\Model\TaxonInterface;

/**
 * @author Anna Walasek <anna.walasek@lakion.com>
 */
final class ProductTaxonSpec extends ObjectBehavior
{
    function it_implements_product_taxon_interface(): void
    {
        $this->shouldImplement(ProductTaxonInterface::class);
    }

    function it_has_mutable_product_field(ProductInterface $product): void
    {
        $this->setProduct($product);
        $this->getProduct()->shouldReturn($product);
    }

    function it_has_mutable_taxon_field(TaxonInterface $taxon): void
    {
        $this->setTaxon($taxon);
        $this->getTaxon()->shouldReturn($taxon);
    }

    function it_has_mutable_position_field(): void
    {
        $this->setPosition(1);
        $this->getPosition()->shouldReturn(1);
    }
}
