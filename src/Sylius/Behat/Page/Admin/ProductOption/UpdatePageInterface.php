<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Page\Admin\ProductOption;

use Sylius\Behat\Page\Admin\Crud\UpdatePageInterface as BaseUpdatePageInterface;

/**
 * @author Grzegorz Sadowski <grzegorz.sadowski@lakion.com>
 */
interface UpdatePageInterface extends BaseUpdatePageInterface
{
    /**
     * @return bool
     */
    public function isCodeDisabled();

    /**
     * @param string $name
     * @param string $languageCode
     */
    public function nameItIn($name, $languageCode);

    /**
     * @param string $optionValue
     */
    public function isThereOptionValue($optionValue);

    /**
     * @param string $code
     * @param string $value
     */
    public function addOptionValue($code, $value);

    /**
     * @param string $optionValue
     */
    public function removeOptionValue($optionValue);
}
