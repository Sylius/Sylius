<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Context\Ui;

use Behat\Behat\Context\Context;
use Behat\Mink\Exception\ElementNotFoundException;
use Sylius\Behat\Page\Admin\Customer\ShowPageInterface;
use Sylius\Component\Core\Test\Services\SharedStorageInterface;

/**
 * @author Magdalena Banasiak <magdalena.banasiak@lakion.com>
 */
final class CustomerContext implements Context
{
    /**
     * @var SharedStorageInterface
     */
    private $sharedStorage;

    /**
     * @var ShowPageInterface
     */
    private $customerShowPage;

    /**
     * @param SharedStorageInterface $sharedStorage
     * @param ShowPageInterface $customerShowPage
     */
    public function __construct(
        SharedStorageInterface $sharedStorage,
        ShowPageInterface $customerShowPage
    ) {
        $this->sharedStorage = $sharedStorage;
        $this->customerShowPage = $customerShowPage;
    }

    /**
     * @Then I should not be able to delete it again
     */
    public function iShouldNotBeAbleToDeleteCustomerAgain()
    {
        $customer = $this->sharedStorage->get('customer');

        $this->customerShowPage->open(['id' => $customer->getId()]);

        expect($this->customerShowPage)->toThrow(ElementNotFoundException::class)->during('deleteAccount');
    }

    /**
     * @Then the customer with this email should still exist
     */
    public function customerShouldStillExist()
    {
        $deletedUser = $this->sharedStorage->get('deleted_user');

        $this->customerShowPage->open(['id' => $deletedUser->getCustomer()->getId()]);

        expect($this->customerShowPage->isRegistered())->toBe(false);
    }
}
