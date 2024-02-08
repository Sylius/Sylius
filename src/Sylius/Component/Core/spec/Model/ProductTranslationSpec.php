<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace spec\Sylius\Component\Core\Model;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\ProductTranslationInterface;
use Sylius\Component\Product\Model\ProductTranslation as BaseProductTranslation;

final class ProductTranslationSpec extends ObjectBehavior
{
    function it_implements_a_core_product_interface(): void
    {
        $this->shouldImplement(ProductTranslationInterface::class);
    }

    function it_extends_a_product_translation_model(): void
    {
        $this->shouldHaveType(BaseProductTranslation::class);
    }

    function it_does_not_have_a_short_description_by_default(): void
    {
        $this->getShortDescription()->shouldReturn(null);
    }

    function its_short_description_is_mutable(): void
    {
        $this->setShortDescription('Amazing product...');
        $this->getShortDescription()->shouldReturn('Amazing product...');
    }
}
