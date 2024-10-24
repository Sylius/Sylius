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

namespace spec\Sylius\Component\Promotion\Model;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Promotion\Model\CatalogPromotionTranslation;
use Sylius\Component\Promotion\Model\CatalogPromotionTranslationInterface;

final class CatalogPromotionTranslationSpec extends ObjectBehavior
{
    function it_is_initializable(): void
    {
        $this->shouldHaveType(CatalogPromotionTranslation::class);
    }

    function it_implements_catalog_promotion_translation_interface(): void
    {
        $this->shouldImplement(CatalogPromotionTranslationInterface::class);
    }

    function it_has_no_id_by_default(): void
    {
        $this->getId()->shouldReturn(null);
    }

    function its_label_is_mutable(): void
    {
        $this->setLabel('Mugs discount');
        $this->getLabel()->shouldReturn('Mugs discount');
    }

    function its_description_is_mutable(): void
    {
        $this->setDescription('Discount on every mug.');
        $this->getDescription()->shouldReturn('Discount on every mug.');
    }
}
