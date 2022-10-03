<?php

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\DataFixtures\Transformer;

use Sylius\Bundle\CoreBundle\DataFixtures\Factory\CountryFactoryInterface;

trait TransformCountryAttributeTrait
{
    private CountryFactoryInterface $countryFactory;

    private function transformCountryAttribute(array $attributes): array
    {
        if (\is_string($attributes['country'])) {
            $attributes['country'] = $this->countryFactory::findOrCreate(['code' => $attributes['country']]);
        }

        return $attributes;
    }
}
