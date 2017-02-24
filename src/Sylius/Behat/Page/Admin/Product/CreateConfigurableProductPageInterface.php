<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Page\Admin\Product;

use Sylius\Behat\Page\Admin\Crud\CreatePageInterface as BaseCreatePageInterface;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
interface CreateConfigurableProductPageInterface extends BaseCreatePageInterface
{
    /**
     * @param string $optionName
     */
    public function selectOption($optionName);

    /**
     * @param string $code
     */
    public function specifyCode($code);

    /**
     * @param string $name
     * @param string $localeCode
     */
    public function nameItIn($name, $localeCode);

    /**
     * @param string $path
     * @param string $type
     */
    public function attachImage($path, $type = null);
}
