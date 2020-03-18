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

namespace Sylius\Behat\Context\Api\Admin;

use Behat\Behat\Context\Context;
use Sylius\Behat\Client\ApiClientInterface;
use Sylius\Behat\Client\ResponseCheckerInterface;
use Sylius\Component\Core\Model\ShippingMethodInterface;
use Symfony\Component\VarDumper\VarDumper;
use Webmozart\Assert\Assert;

final class ManagingShippingMethodsContext implements Context
{
    /** @var ApiClientInterface */
    private $client;

    /** @var ResponseCheckerInterface */
    private $responseChecker;

    public function __construct(ApiClientInterface $client, ResponseCheckerInterface $responseChecker)
    {
        $this->client = $client;
        $this->responseChecker = $responseChecker;
    }

    /**
     * @When I want to browse shipping methods
     */
    public function iBrowseShippingMethods(): void
    {
        $this->client->index();
    }

    /**
     * @When I delete shipping method :shippingMethod
     */
    public function iDeleteShippingMethod(ShippingMethodInterface $shippingMethod): void
    {
        $this->client->delete($shippingMethod->getCode());
    }

    /**
     * @Then I should see :count shipping methods in the list
     */
    public function iShouldSeeShippingMethodsInTheList(int $count): void
    {
        Assert::count($this->responseChecker->getCollection($this->client->getLastResponse()), $count);
    }

    /**
     * @Then the shipping method :shippingMethodName should be in the registry
     */
    public function theShippingMethodShouldAppearInTheRegistry(string $shippingMethodName): void
    {
        Assert::true(
            $this->responseChecker->hasItemWithValue($this->client->getLastResponse(), 'name', $shippingMethodName),
            sprintf('Shipping method with name %s does not exists', $shippingMethodName)
        );
    }

    /**
     * @Then I should be notified that it has been successfully deleted
     */
    public function iShouldBeNotifiedThatItHasBeenSuccessfullyDeleted(): void
    {
        Assert::true(
            $this->responseChecker->isDeletionSuccessful($this->client->getLastResponse()),
            'Shipping method could not be deleted'
        );
    }

    /**
     * @Then /^(this shipping method) should no longer exist in the registry$/
     */
    public function thisShippingMethodShouldNoLongerExistInTheRegistry(ShippingMethodInterface $shippingMethod): void {
        $shippingMethodName = $shippingMethod->getName();

        Assert::false(
            $this->responseChecker->hasItemWithValue($this->client->index(), 'name', $shippingMethodName),
            sprintf('Shipping category with name %s exist', $shippingMethodName)
        );
    }
}
