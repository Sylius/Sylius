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

use Sylius\Component\Core\Model\PaymentMethodInterface;
use Sylius\Component\Locale\Model\LocaleInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

final class PaymentMethodUpdater implements PaymentMethodUpdaterInterface
{
    public function __construct(private RepositoryInterface $localeRepository)
    {
    }

    public function update(PaymentMethodInterface $paymentMethod, array $attributes): void
    {
        $paymentMethod->getGatewayConfig()->setGatewayName($attributes['gateway_name']);
        $paymentMethod->getGatewayConfig()->setConfig($attributes['gateway_config']);

        $paymentMethod->setCode($attributes['code']);
        $paymentMethod->setEnabled($attributes['enabled']);

        foreach ($this->getLocales() as $localeCode) {
            $paymentMethod->setCurrentLocale($localeCode);
            $paymentMethod->setFallbackLocale($localeCode);

            $paymentMethod->setName($attributes['name']);
            $paymentMethod->setDescription($attributes['description']);
            $paymentMethod->setInstructions($attributes['instructions']);
        }

        foreach ($attributes['channels'] as $channel) {
            $paymentMethod->addChannel($channel);
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
