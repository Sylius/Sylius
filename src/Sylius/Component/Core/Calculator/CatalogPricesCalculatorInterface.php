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

namespace Sylius\Component\Core\Calculator;

/**
 * Calculates prices for catalog display purposes (product listings, detail pages, API responses).
 *
 * This is intentionally distinct from ProductVariantPricesCalculatorInterface used in cart/order
 * processing, allowing independent customization of display pricing (e.g., B2B list prices,
 * EU Omnibus Directive compliance, geo-pricing, OSS VAT destination-based rules).
 *
 * Decorate this interface to customize catalog display pricing without any risk of side effects
 * on cart totals, order processing, or promotion engines.
 */
interface CatalogPricesCalculatorInterface extends ProductVariantPricesCalculatorInterface
{
}
