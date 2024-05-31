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

namespace Sylius\Behat\Page\Admin\Product\ConfigurableProduct;

use Sylius\Behat\Page\Admin\Crud\CreatePageInterface as BaseCreatePageInterface;

interface CreateConfigurableProductPageInterface extends BaseCreatePageInterface
{
    public function selectOption(string $optionName): void;

    public function specifyCode(string $code): void;
}
