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

use Sylius\Behat\Page\Admin\Crud\CreatePageInterface as BaseCreatePageInterface;

/**
 * @author Grzegorz Sadowski <grzegorz.sadowski@lakion.com>
 */
interface CreatePageInterface extends BaseCreatePageInterface
{
    /**
     * @param string $code
     */
    public function specifyCode($code);

    /**
     * @param string $name
     * @param string $language
     */
    public function nameItIn($name, $language);

    /**
     * @param string $code
     * @param string $value
     */
    public function addOptionValue($code, $value);

    /**
     * @param string $message
     */
    public function checkValidationMessageForOptionValues($message);
}
