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

namespace Sylius\Behat\Page\Admin\ExchangeRate;

use Sylius\Behat\Page\Admin\Crud\CreatePageInterface as BaseCreatePage;

interface CreatePageInterface extends BaseCreatePage
{
    public function specifyRatio(float $ratio);

    public function chooseSourceCurrency(string $currency);

    public function chooseTargetCurrency(string $currency);

    public function hasFormValidationError(string $expectedMessage): bool;
}
