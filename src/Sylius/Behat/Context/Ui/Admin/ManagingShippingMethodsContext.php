<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Context\Ui\Admin;

use Behat\Behat\Context\Context;
use Sylius\Behat\Page\Admin\ShippingMethod\IndexPageInterface;
use Sylius\Behat\Page\Admin\ShippingMethod\ShowPageInterface;
use Sylius\Behat\Page\UnexpectedPageException;
use Sylius\Component\Core\Model\ShippingMethodInterface;
use Sylius\Component\Core\Test\Services\SharedStorageInterface;

/**
 * @author Jan Góralski <jan.goralski@lakion.com>
 */
final class ManagingShippingMethodsContext implements Context
{
    /**
     * @var SharedStorageInterface
     */
    private $sharedStorage;

    /**
     * @var ShowPageInterface
     */
    private $shippingMethodShowPage;

    /**
     * @var IndexPageInterface
     */
    private $shippingMethodIndexPage;

    /**
     * @param SharedStorageInterface $sharedStorage
     * @param ShowPageInterface $shippingMethodDetailsPage
     * @param IndexPageInterface $shippingMethodIndexPage
     */
    public function __construct(
        SharedStorageInterface $sharedStorage,
        ShowPageInterface $shippingMethodDetailsPage,
        IndexPageInterface $shippingMethodIndexPage
    ) {
        $this->sharedStorage = $sharedStorage;
        $this->shippingMethodShowPage = $shippingMethodDetailsPage;
        $this->shippingMethodIndexPage = $shippingMethodIndexPage;
    }

    /**
     * @When /^I try to delete ("[^"]+" shipping method)$/
     */
    public function iTryToDeleteShippingMethod(ShippingMethodInterface $shippingMethod)
    {
        $this->sharedStorage->set('shipping_method', $shippingMethod);

        $this->shippingMethodShowPage->open(['id' => $shippingMethod->getId()]);
        $this->shippingMethodShowPage->pressDelete();
    }

    /**
     * @Then I should be notified that it is in use
     */
    public function iShouldBeNotifiedItIsUsed()
    {
        $message = 'Cannot delete, the shipping method is in use.';

        expect($this->shippingMethodShowPage->flashContainsMessage($message))->toBe(true);
    }

    /**
     * @Then :it should be successfully removed
     */
    public function shippingMethodShouldBeRemoved(ShippingMethodInterface $shippingMethod)
    {
        expect($this->shippingMethodIndexPage->isOpen())->toBe(true);

        expect($this->shippingMethodIndexPage->isThereShippingMethodNamed($shippingMethod->getName()))->toBe(false);
    }

    /**
     * @Then :it should not be removed
     */
    public function shippingMethodShouldNotBeRemoved(ShippingMethodInterface $shippingMethod)
    {
        expect($this->shippingMethodShowPage->isOpen(['id' => $shippingMethod->getId()]))->toBe(true);

        expect(
            $this->shippingMethodShowPage->verify(['id' => $shippingMethod->getId()])
        )->toNotThrow(UnexpectedPageException::class);
    }
}
