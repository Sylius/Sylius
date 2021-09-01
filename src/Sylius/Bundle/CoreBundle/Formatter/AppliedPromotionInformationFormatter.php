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
    public function format(CatalogPromotionInterface $catalogPromotion): array
    {
        /** @var CatalogPromotionTranslationInterface $translation */
        $translation = $catalogPromotion->getTranslations()->first();
        /** @var string $code */
        $code = $catalogPromotion->getCode();
        /** @var string $name */
        $name = $translation->getLabel();

        return [$code => ['name' => $name]];
    }
}
