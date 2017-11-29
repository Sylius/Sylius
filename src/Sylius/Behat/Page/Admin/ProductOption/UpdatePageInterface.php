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

namespace Sylius\Behat\Page\Admin\ProductOption;

use Sylius\Behat\Page\Admin\Crud\UpdatePageInterface as BaseUpdatePageInterface;

interface UpdatePageInterface extends BaseUpdatePageInterface
{
    /**
     * @return bool
     */
    public function isCodeDisabled(): bool;

    /**
     * @param string $name
     * @param string $languageCode
     */
    public function nameItIn(string $name, string $languageCode): void;

    /**
     * @param string $optionValue
     */
    public function isThereOptionValue(string $optionValue): void;

    /**
     * @param string $code
     * @param string $value
     */
    public function addOptionValue(string $code, string $value): void;

    /**
     * @param string $optionValue
     */
    public function removeOptionValue(string $optionValue): void;
}
