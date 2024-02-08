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

namespace Sylius\Behat\Context\Ui;

use Behat\Behat\Context\Context;
use Behat\Mink\Exception\ElementNotFoundException;
use Sylius\Behat\Page\Admin\Customer\ShowPageInterface;
use Sylius\Behat\Service\SharedStorageInterface;
use Webmozart\Assert\Assert;

final class CustomerContext implements Context
{
    public function __construct(
        private SharedStorageInterface $sharedStorage,
        private ShowPageInterface $customerShowPage,
    ) {
    }

    /**
     * @Then I should not be able to delete it again
     */
    public function iShouldNotBeAbleToDeleteCustomerAgain()
    {
        $customer = $this->sharedStorage->get('customer');

        $this->customerShowPage->open(['id' => $customer->getId()]);

        try {
            $this->customerShowPage->deleteAccount();
        } catch (ElementNotFoundException) {
            return;
        }

        throw new \DomainException('Delete account should throw an exception!');
    }

    /**
     * @Then the customer with this email should still exist
     */
    public function customerShouldStillExist()
    {
        $deletedUser = $this->sharedStorage->get('deleted_user');

        $this->customerShowPage->open(['id' => $deletedUser->getCustomer()->getId()]);

        Assert::false($this->customerShowPage->isRegistered());
    }
}
