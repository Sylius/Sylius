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

namespace Sylius\Bundle\AdminBundle\TwigComponent\Product;

use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\ComponentToolsTrait;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent(name: 'SyliusAdmin.Product.ProductAttributeAutocomplete', template: '@SyliusAdmin/Product/_productAttributeAutocomplete.html.twig')]
final class ProductAttributeAutocompleteComponent
{
    #[LiveProp(writable: true)]
    public ?string $attributeCodes = null;

    use ComponentToolsTrait;
    use DefaultActionTrait;

    #[LiveAction]
    public function addAttribute(): void
    {
        $this->emit('product_attribute_autocomplete:add', ['attributeCodes' => explode(',', $this->attributeCodes)]);
        $this->attributeCodes = '';
        $this->dispatchBrowserEvent('product_attribute_autocomplete:clear');
    }
}
