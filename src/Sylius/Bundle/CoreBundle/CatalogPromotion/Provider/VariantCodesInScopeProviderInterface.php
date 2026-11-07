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

namespace Sylius\Bundle\CoreBundle\CatalogPromotion\Provider;

use Sylius\Component\Promotion\Model\CatalogPromotionScopeInterface;

interface VariantCodesInScopeProviderInterface
{
    /**
     * Returns the codes of the variants the given scope may apply to.
     *
     * The result may be wider than the variants matched by the corresponding
     * VariantInScopeCheckerInterface, but never narrower: channels, promotion state
     * and the "enabled" flag are deliberately not taken into account.
     *
     * @return list<string>
     */
    public function provide(CatalogPromotionScopeInterface $scope): array;
}
