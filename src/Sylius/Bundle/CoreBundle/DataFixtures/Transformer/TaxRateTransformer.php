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

namespace Sylius\Bundle\CoreBundle\DataFixtures\Transformer;

use Psr\EventDispatcher\EventDispatcherInterface;

final class TaxRateTransformer implements TaxRateTransformerInterface
{
    use TransformNameToCodeAttributeTrait;
    use TransformZoneAttributeTrait;
    use TransformTaxCategoryAttributeTrait;

    public function __construct(
        private EventDispatcherInterface $eventDispatcher,
    ) {
    }

    public function transform(array $attributes): array
    {
        $attributes = $this->transformNameToCodeAttribute($attributes);
        $attributes = $this->transformZoneAttribute($this->eventDispatcher, $attributes);

        return $this->transformTaxCategoryAttribute($this->eventDispatcher, $attributes, 'category');
    }
}
