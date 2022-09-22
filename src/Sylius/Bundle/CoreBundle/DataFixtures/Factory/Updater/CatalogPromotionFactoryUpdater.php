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

namespace Sylius\Bundle\CoreBundle\DataFixtures\Factory\Updater;

use Sylius\Component\Core\Model\CatalogPromotionInterface;
use Sylius\Component\Locale\Model\LocaleInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

final class CatalogPromotionFactoryUpdater implements CatalogPromotionFactoryUpdaterInterface
{
    public function __construct(private RepositoryInterface $localeRepository)
    {
    }

    public function update(CatalogPromotionInterface $catalogPromotion, array $attributes): void
    {
        $catalogPromotion->setCode($attributes['code']);
        $catalogPromotion->setName($attributes['name']);
        $catalogPromotion->setPriority($attributes['priority']);
        $catalogPromotion->setExclusive($attributes['exclusive']);
        $catalogPromotion->setStartDate($attributes['start_date']);
        $catalogPromotion->setEndDate($attributes['end_date']);
        $catalogPromotion->setEnabled($attributes['enabled']);

        foreach ($this->getLocales() as $localeCode) {
            $catalogPromotion->setCurrentLocale($localeCode);
            $catalogPromotion->setFallbackLocale($localeCode);

            $catalogPromotion->setLabel($attributes['label']);
            $catalogPromotion->setDescription($attributes['description']);
        }

        foreach ($attributes['channels'] as $channel) {
            $catalogPromotion->addChannel($channel);
        }

        foreach ($attributes['scopes'] as $scope) {
            $catalogPromotion->addScope($scope);
        }

        foreach ($attributes['actions'] as $action) {
            $catalogPromotion->addAction($action);
        }
    }

    private function getLocales(): iterable
    {
        /** @var LocaleInterface[] $locales */
        $locales = $this->localeRepository->findAll();
        foreach ($locales as $locale) {
            yield $locale->getCode();
        }
    }
}
