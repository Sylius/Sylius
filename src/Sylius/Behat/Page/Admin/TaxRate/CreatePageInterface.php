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
    public function specifyCode(string $code);

    public function nameIt(string $name);

    public function specifyAmount(string $amount);

    public function chooseZone(string $name);

    public function chooseCategory(string $name);

    public function chooseCalculator(string $name);

    public function chooseIncludedInPrice();
}
