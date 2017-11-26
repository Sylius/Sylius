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

namespace Sylius\Behat\Page\Admin\TaxRate;

use Sylius\Behat\Page\Admin\Crud\CreatePageInterface as BaseCreatePageInterface;

interface CreatePageInterface extends BaseCreatePageInterface
{
    /**
     * @param string $code
     */
    public function specifyCode(string $code): void;

    /**
     * @param string $name
     */
    public function nameIt(string $name): void;

    /**
     * @param string $amount
     */
    public function specifyAmount(string $amount): void;

    /**
     * @param string $name
     */
    public function chooseZone(string $name): void;

    /**
     * @param string $name
     */
    public function chooseCategory(string $name): void;

    /**
     * @param string $name
     */
    public function chooseCalculator(string $name): void;

    public function chooseIncludedInPrice(): void;
}
