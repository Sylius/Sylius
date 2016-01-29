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
use Sylius\Behat\Page\Customer\CustomerShowPage;
use Sylius\Component\Core\Test\Services\SharedStorageInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

/**
 * @author Magdalena Banasiak <magdalena.banasiak@lakion.com>
 */
class CustomerContext implements Context
{
    /**
     * @var SharedStorageInterface
     */
    private $sharedStorage;

    /**
     * @var RepositoryInterface
     */
    private $customerRepository;

    /**
     * @var CustomerShowPage
     */
    private $customerShowPage;

    /**
     * @param SharedStorageInterface $sharedStorage
     * @param RepositoryInterface $customerRepository
     * @param CustomerShowPage $customerShowPage
     */
    public function __construct(
        SharedStorageInterface $sharedStorage,
        RepositoryInterface $customerRepository,
        CustomerShowPage $customerShowPage
    ) {
        $this->sharedStorage = $sharedStorage;
        $this->customerRepository = $customerRepository;
        $this->customerShowPage = $customerShowPage;
    }

    /**
     * @Then there should be customer with email :email
     */
    public function thereShouldBeCustomerIdentifiedBy($email)
    {
        $customer = $this->customerRepository->findOneBy(array('email' =>$email));

        $this->customerShowPage->open(array('id' => $customer->getId()));

        if (true === $this->customerShowPage->isThisCustomerRegistered($email)) {
            throw new \InvalidArgumentException('This customer is a registered user, when it should not.');
        }
    }
}
