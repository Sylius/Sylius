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

use Sylius\Behat\Page\Admin\Crud\CreatePageInterface as BaseCreatePageInterface;

interface CreatePageInterface extends BaseCreatePageInterface
{
    /**
     * @param string $code
     */
    public function specifyCode(string $code): void;

    /**
     * @param string $name
     * @param string $language
     */
    public function nameItIn(string $name, string $language): void;

    /**
     * @param string $code
     * @param string $value
     */
    public function addOptionValue(string $code, string $value): void;

    /**
     * @param string $message
     */
    public function checkValidationMessageForOptionValues(string $message): void;
}
