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

namespace spec\Sylius\Bundle\CoreBundle\Formatter;

use Doctrine\Common\Collections\ArrayCollection;
use PhpSpec\ObjectBehavior;
use Sylius\Bundle\CoreBundle\Formatter\AppliedPromotionInformationFormatterInterface;
use Sylius\Component\Core\Model\CatalogPromotionInterface;
use Sylius\Component\Promotion\Model\CatalogPromotionTranslationInterface;

final class AppliedPromotionInformationFormatterSpec extends ObjectBehavior
{
    function it_implements_applied_promotion_information_formatter_interface(): void
    {
        $this->shouldImplement(AppliedPromotionInformationFormatterInterface::class);
    }

    function it_formats_applied_promotion_information(
        CatalogPromotionInterface $catalogPromotion,
        CatalogPromotionTranslationInterface $translation
    ): void {
        $catalogPromotion->getTranslations()->willReturn(new ArrayCollection([$translation->getWrappedObject()]));

        $translation->getLabel()->willReturn('Winter sale');
        $translation->getLocale()->willReturn('en_US');
        $translation->getDescription()->willReturn('Winter sale description');
        $catalogPromotion->getCode()->willReturn('winter_sale');
        $catalogPromotion->isExclusive()->willReturn(true);

        $this->format($catalogPromotion)->shouldReturn([
            'winter_sale' => [
                'is_exclusive' => true,
                'translations' => [
                    'en_US' => ['name' => 'Winter sale', 'description' => 'Winter sale description']
                ]
            ]
        ]);
    }
}
