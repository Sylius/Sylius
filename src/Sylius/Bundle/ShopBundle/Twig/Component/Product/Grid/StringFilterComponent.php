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

namespace Sylius\Bundle\ShopBundle\Twig\Component\Product\Grid;

use Sylius\Component\Grid\Twig\Component\ComponentWithFilterTrait;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;
use Symfony\UX\TwigComponent\Attribute\PostMount;

/**
 * Search input for the "shop_string" grid filter, applied while typing.
 * The type of the search is the one configured on the filter (e.g. "contains").
 *
 * @experimental
 */
class StringFilterComponent
{
    use ComponentWithFilterTrait;
    use DefaultActionTrait;

    #[LiveProp(writable: true)]
    public string $value = '';

    /**
     * Higher priority than ComponentWithFormTrait::initializeForm() so the form is built with the mounted value.
     */
    #[PostMount(priority: 10)]
    public function initializeFilterValue(): void
    {
        $criterion = $this->criteria[$this->filter->getName()] ?? null;

        // Same criteria shapes as the StringFilter: a scalar value or an array with a "value" key
        if (is_array($criterion)) {
            $criterion = $criterion['value'] ?? null;
        }

        $this->value = is_scalar($criterion) ? (string) $criterion : '';
    }

    protected function reset(): void
    {
        $this->value = '';
    }

    protected function toArray(): array
    {
        return [
            $this->filter->getName() => ['value' => $this->value],
        ];
    }
}
