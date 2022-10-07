<?php

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\DataFixtures\Transformer;

use Sylius\Bundle\CoreBundle\DataFixtures\Event\FindOrCreateResourceEvent;
use Sylius\Bundle\CoreBundle\DataFixtures\Factory\CurrencyFactoryInterface;

trait TransformCurrenciesAttributeTrait
{
    private CurrencyFactoryInterface $currencyFactory;

    private function transformCurrenciesAttribute(array $attributes): array
    {
        $currencies = [];
        foreach ($attributes['currencies'] as $currency) {
            if (\is_string($currency)) {
                /** @var FindOrCreateResourceEvent $event */
                $event = $this->eventDispatcher->dispatch(new FindOrCreateResourceEvent(CurrencyFactoryInterface::class, ['code' => $currency]));

                $currency = $event->getResource();
            }

            $currencies[] = $currency;
        }
        $attributes['currencies'] = $currencies;

        return $attributes;
    }
}
