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

namespace Sylius\Behat\Page\Admin\Product;

use Sylius\Behat\Page\Admin\Crud\CreatePageInterface as BaseCreatePageInterface;

interface CreateConfigurableProductPageInterface extends BaseCreatePageInterface
{
    public function selectOption(string $optionName);

    public function specifyCode(string $code);

    public function nameItIn(string $name, string $localeCode);

    /**
     * @param string $type
     */
    public function attachImage(string $path, string $type = null);
}
