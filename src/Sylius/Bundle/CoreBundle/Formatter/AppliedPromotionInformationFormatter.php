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

namespace Sylius\Bundle\CoreBundle\Formatter;

use Sylius\Component\Core\Model\CatalogPromotionInterface;
use Sylius\Component\Promotion\Model\CatalogPromotionTranslationInterface;

final class AppliedPromotionInformationFormatter implements AppliedPromotionInformationFormatterInterface
{
    public function format(CatalogPromotionInterface $catalogPromotion): CatalogPromotionInterface
    {
        $translationLabels = [];

        /** @var CatalogPromotionTranslationInterface $translation */
        foreach ($catalogPromotion->getTranslations() as $translation) {
            $translationLabels[$translation->getLocale()] = ['name' => $translation->getLabel(), 'description' => $translation->getDescription()];
        }


        return $catalogPromotion;
    }
}
