<?php

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\DataFixtures\Transformer;

use Psr\EventDispatcher\EventDispatcherInterface;
use Sylius\Bundle\CoreBundle\DataFixtures\Util\FindOrCreateCurrencyTrait;

trait TransformCurrenciesAttributeTrait
{
    use FindOrCreateCurrencyTrait;

    private function transformCurrenciesAttribute(EventDispatcherInterface $eventDispatcher, array $attributes): array
    {
        $currencies = [];
        foreach ($attributes['currencies'] as $currency) {
            if (\is_string($currency)) {
                $currency = $this->findOrCreateCurrency($eventDispatcher, ['code' => $currency]);
            }

            $currencies[] = $currency;
        }
        $attributes['currencies'] = $currencies;

        return $attributes;
    }
}
