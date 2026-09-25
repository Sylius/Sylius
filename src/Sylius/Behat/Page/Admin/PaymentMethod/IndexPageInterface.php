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

namespace Sylius\Behat\Page\Admin\PaymentMethod;

use Sylius\Behat\Page\Admin\Crud\IndexPageInterface as BaseIndexPageInterface;

interface IndexPageInterface extends BaseIndexPageInterface
{
    public function countPaymentMethodsWithName(string $name): int;

    /** @return string[] */
    public function getPaymentMethodNamesInOrder(): array;

    public function deletePaymentMethodWithName(string $name): void;

    public function checkPaymentMethodWithName(string $name): void;
}
