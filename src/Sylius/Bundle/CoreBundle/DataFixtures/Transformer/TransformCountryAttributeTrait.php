<?php

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\DataFixtures\Transformer;

use Sylius\Bundle\CoreBundle\DataFixtures\Util\FindOrCreateCountryTrait;

trait TransformCountryAttributeTrait
{
    use FindOrCreateCountryTrait;

    private function transformCountryAttribute(array $attributes): array
    {
        if (\is_string($attributes['country'])) {
            $attributes['country'] = $this->findOrCreateCountry(['code' => $attributes['country']]);
        }

        return $attributes;
    }
}
