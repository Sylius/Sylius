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
use Sylius\Behat\Page\Shipping\ShippingMethodIndexPage;
use Sylius\Behat\Page\Shipping\ShippingMethodShowPage;
use Sylius\Component\Core\Model\ShippingMethodInterface;
use Sylius\Component\Core\Test\Services\SharedStorageInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

/**
 * @author Jan Góralski <jan.goralski@lakion.com>
 */
final class ShippingContext implements Context
{
    /**
     * @var RepositoryInterface
     */
    private $shippingMethodRepository;

    /**
     * @var SharedStorageInterface
     */
    private $sharedStorage;

    /**
     * @var ShippingMethodIndexPage
     */
    private $shippingMethodIndexPage;

    /**
     * @var ShippingMethodShowPage
     */
    private $shippingMethodShowPage;

    /**
     * @param RepositoryInterface $shippingMethodRepository
     * @param SharedStorageInterface $sharedStorage
     * @param ShippingMethodIndexPage $shippingMethodIndexPage
     * @param ShippingMethodShowPage $shippingMethodShowPage
     */
    public function __construct(
        RepositoryInterface $shippingMethodRepository,
        SharedStorageInterface $sharedStorage,
        ShippingMethodIndexPage $shippingMethodIndexPage,
        ShippingMethodShowPage $shippingMethodShowPage
    ) {
        $this->shippingMethodRepository = $shippingMethodRepository;
        $this->sharedStorage = $sharedStorage;
        $this->shippingMethodIndexPage = $shippingMethodIndexPage;
        $this->shippingMethodShowPage = $shippingMethodShowPage;
    }

    /**
     * @When /^I try to delete shipping method "([^"]*)"$/
     */
    public function iDeleteShippingMethod($shippingMethodName)
    {
        $shippingMethod = $this->findShippingMethodByName($shippingMethodName);
        $this->sharedStorage->setCurrentResource('shippingMethod', $shippingMethod);

        $this->shippingMethodShowPage->open(['id' => $shippingMethod->getId()]);
        $this->shippingMethodShowPage->deleteMethod();
    }

    /**
     * @Then I should be notified that it is in use
     */
    public function iShouldBeNotified()
    {
        $message = 'Cannot delete, the shipping method is in use.';

        if (!$this->shippingMethodShowPage->flashContainsMessage($message)) {
            throw new \Exception(sprintf('Message "%s" was not found in flash.', $message));
        }
    }

    /**
     * @Then it should be successfully removed
     */
    public function itShouldBeRemoved()
    {
        $removedMethodName = $this->sharedStorage->getCurrentResource('shippingMethod')->getName();
        $this->shippingMethodIndexPage->open();

        if ($this->shippingMethodIndexPage->listContainsShippingMethod($removedMethodName)) {
            throw new \Exception(sprintf('Shipping method %s was not removed.', $removedMethodName));
        }
    }

    /**
     * @Then /^shipping method "([^"]*)" should not be removed$/
     * @Then the shipping method should not be removed
     */
    public function shippingMethodShouldNotBeRemoved($name = null)
    {
        $this->shippingMethodIndexPage->open();

        if (!$name) {
            $name = $this->sharedStorage->getCurrentResource('shippingMethod')->getName();
        }

        if (!$this->shippingMethodIndexPage->listContainsShippingMethod($name)) {
            throw new \Exception(sprintf('Shipping method %s was removed which should not have happened.', $name));
        }
    }

    /**
     * @param string $name
     *
     * @return ShippingMethodInterface
     *
     * @throws \Exception
     */
    private function findShippingMethodByName($name)
    {
        $shippingMethod = $this->shippingMethodRepository->findOneBy(['name' => $name]);
        if (null === $shippingMethod) {
            throw new \Exception(sprintf('Shipping method %s does not exist.', $name));
        }

        return $shippingMethod;
    }
}
