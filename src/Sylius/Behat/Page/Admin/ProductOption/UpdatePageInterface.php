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

namespace Sylius\Behat\Page\Admin\ProductOption;

use Sylius\Behat\Page\Admin\Crud\UpdatePageInterface as BaseUpdatePageInterface;

interface UpdatePageInterface extends BaseUpdatePageInterface
{
    public function isCodeDisabled(): bool;

    public function nameItIn(string $name, string $languageCode): void;

    public function isThereOptionValue(string $optionValue): bool;

    public function addOptionValue(string $code, string $value): void;

    public function removeOptionValue(string $optionValue): void;
}
