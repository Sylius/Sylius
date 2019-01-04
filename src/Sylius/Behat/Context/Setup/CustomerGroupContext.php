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

namespace Sylius\Behat\Context\Setup;

use Behat\Behat\Context\Context;
use Sylius\Behat\Service\SharedStorageInterface;
use Sylius\Component\Core\Formatter\StringInflector;
use Sylius\Component\Customer\Model\CustomerGroupInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

final class CustomerGroupContext implements Context
{
    /** @var SharedStorageInterface */
    private $sharedStorage;

    /** @var RepositoryInterface */
    private $customerGroupRepository;

    /** @var FactoryInterface */
    private $customerGroupFactory;

    public function __construct(
        SharedStorageInterface $sharedStorage,
        RepositoryInterface $customerGroupRepository,
        FactoryInterface $customerGroupFactory
    ) {
        $this->sharedStorage = $sharedStorage;
        $this->customerGroupRepository = $customerGroupRepository;
        $this->customerGroupFactory = $customerGroupFactory;
    }

    /**
     * @Given the store has a customer group :name
     * @Given the store has a customer group :name with :code code
     */
    public function theStoreHasACustomerGroup($name, $code = null)
    {
        $this->createCustomerGroup($name, $code);
    }

    /**
     * @Given the store has customer groups :firstName and :secondName
     * @Given the store has customer groups :firstName, :secondName and :thirdName
     */
    public function theStoreHasCustomerGroups(string ...$names): void
    {
        foreach ($names as $name) {
            $this->theStoreHasACustomerGroup($name);
        }
    }

    /**
     * @param string $name
     * @param string $code
     */
    private function createCustomerGroup($name, $code)
    {
        /** @var CustomerGroupInterface $customerGroup */
        $customerGroup = $this->customerGroupFactory->createNew();

        $customerGroup->setCode($code ?: $this->generateCodeFromName($name));
        $customerGroup->setName(ucfirst($name));

        $this->sharedStorage->set('customer_group', $customerGroup);
        $this->customerGroupRepository->add($customerGroup);
    }

    /**
     * @param string $name
     *
     * @return string
     */
    private function generateCodeFromName($name)
    {
        return StringInflector::nameToCode($name);
    }
}
