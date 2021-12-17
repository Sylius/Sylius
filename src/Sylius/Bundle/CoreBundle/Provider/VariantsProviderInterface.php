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

namespace Sylius\Bundle\CoreBundle\Provider;

use Sylius\Component\Promotion\Model\CatalogPromotionScopeInterface;

interface VariantsProviderInterface
{
    public function getType(): string;

    public function supports(CatalogPromotionScopeInterface $catalogPromotionScopeType): bool;

    public function provideEligibleVariants(CatalogPromotionScopeInterface $scope): array;
}
