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

namespace Sylius\Behat\Page\Admin\Customer;

use Sylius\Behat\Page\Admin\Crud\IndexPageInterface as BaseIndexPageInterface;
use Sylius\Component\Customer\Model\CustomerInterface;

interface IndexPageInterface extends BaseIndexPageInterface
{
    public function getCustomerAccountStatus(CustomerInterface $customer): string;

    public function isCustomerVerified(CustomerInterface $customer): bool;

    public function specifyFilterGroup(string $groupName): void;
}
