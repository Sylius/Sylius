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
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;
use Symfony\UX\TwigComponent\Attribute\PostMount;

/**
 * Multiple choice of taxons (rendered as checkboxes) for the "shop_product_taxon" grid filter.
 *
 * @experimental
 */
class TaxonFilterComponent
{
    use ComponentWithFilterTrait;
    use DefaultActionTrait;

    /** @var list<string> selected taxon codes */
    #[LiveProp(writable: true)]
    public array $value = [];

    /**
     * Higher priority than ComponentWithFormTrait::initializeForm() so the form is built with the mounted value.
     */
    #[PostMount(priority: 10)]
    public function initializeFilterValue(): void
    {
        $criterion = $this->criteria[$this->filter->getName()] ?? [];

        $this->value = array_values(array_filter(
            is_array($criterion) ? $criterion : [$criterion],
            static fn (mixed $code): bool => is_string($code) && '' !== $code,
        ));
    }

    #[LiveAction]
    public function clear(): void
    {
        $this->reset();
        $this->onChange();
    }

    protected function reset(): void
    {
        $this->value = [];
    }

    protected function toArray(): array
    {
        return [
            $this->filter->getName() => array_values(array_filter(
                $this->value,
                static fn (mixed $code): bool => is_string($code) && '' !== $code,
            )),
        ];
    }
}
