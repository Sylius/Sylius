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

namespace Sylius\Behat\Page\Admin\ProductReview;

use Sylius\Behat\Page\Admin\Crud\IndexPageInterface as BaseIndexPageInterface;

interface IndexPageInterface extends BaseIndexPageInterface
{
    /** @param array<string, string> $parameters */
    public function accept(array $parameters): void;

    /** @param array<string, string> $parameters */
    public function reject(array $parameters): void;

    public function filterByState(string $state): void;

    public function filterByTitle(string $phrase): void;

    public function filterByProduct(string $productName): void;
}
