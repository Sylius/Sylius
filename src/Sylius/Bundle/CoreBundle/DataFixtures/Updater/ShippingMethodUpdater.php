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

namespace Sylius\Bundle\CoreBundle\DataFixtures\Updater;

use Faker\Factory;
use Faker\Generator;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ShippingMethodInterface;
use Sylius\Component\Locale\Model\LocaleInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\Component\Shipping\Calculator\DefaultCalculators;

final class ShippingMethodUpdater implements ShippingMethodUpdaterInterface
{
    private Generator $faker;

    public function __construct(private RepositoryInterface $localeRepository)
    {
        $this->faker = Factory::create();
    }

    public function update(ShippingMethodInterface $shippingMethod, array $attributes): void
    {
        $shippingMethod->setCode($attributes['code']);
        $shippingMethod->setZone($attributes['zone']);
        $shippingMethod->setTaxCategory($attributes['tax_category']);
        $shippingMethod->setCategory($attributes['category']);
        $shippingMethod->setArchivedAt($attributes['archived_at']);
        $shippingMethod->setEnabled($attributes['enabled']);

        foreach ($this->getLocales() as $localeCode) {
            $shippingMethod->setCurrentLocale($localeCode);
            $shippingMethod->setFallbackLocale($localeCode);

            $shippingMethod->setName($attributes['name']);
            $shippingMethod->setDescription($attributes['description']);
        }

        foreach ($attributes['channels'] as $channel) {
            $shippingMethod->addChannel($channel);
        }

        if (null === $attributes['calculator']) {
            $configuration = [];
            /** @var ChannelInterface $channel */
            foreach ($attributes['channels'] as $channel) {
                $configuration[$channel->getCode()] = ['amount' => $this->faker->numberBetween(100, 1000)];
            }

            $attributes['calculator'] = [
                'type' => DefaultCalculators::FLAT_RATE,
                'configuration' => $configuration,
            ];
        }

        $shippingMethod->setCalculator($attributes['calculator']['type']);
        $shippingMethod->setConfiguration($attributes['calculator']['configuration']);
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
