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

use ApiPlatform\Core\Api\IriConverterInterface;
use Behat\Behat\Context\Context;
use Sylius\Behat\Client\ApiClientInterface;
use Sylius\Behat\Client\ResponseCheckerInterface;
use Sylius\Component\Addressing\Model\ZoneInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ShippingMethodInterface;
use Webmozart\Assert\Assert;

final class ManagingShippingMethodsContext implements Context
{
    /** @var ApiClientInterface */
    private $client;

    /** @var ResponseCheckerInterface */
    private $responseChecker;

    /** @var IriConverterInterface */
    private $iriConverter;

    public function __construct(
        ApiClientInterface $client,
        ResponseCheckerInterface $responseChecker,
        IriConverterInterface $iriConverter
    ) {
        $this->client = $client;
        $this->responseChecker = $responseChecker;
        $this->iriConverter = $iriConverter;
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
     * @When I want to create a new shipping method
     */
    public function iWantToCreateANewShippingMethod(): void
    {
        $this->client->buildCreateRequest();
    }

    /**
     * @When I add it
     */
    public function iAddIt(): void
    {
        $this->client->create();
    }

    /**
     * @When I specify its code as :code
     */
    public function iSpecifyItsCodeAs(string $code): void
    {
        $this->client->addRequestData('code', $code);
    }

    /**
     * @When I specify its position as :position
     */
    public function iSpecifyItsPositionAs(int $position): void
    {
        $this->client->addRequestData('position', $position);
    }

    /**
     * @When I name it :name in :localeCode
     */
    public function iNameItIn(string $name, string $localeCode): void
    {
        $data = ['translations' => [$localeCode => ['locale' => $localeCode]]];
        $data['translations'][$localeCode]['name'] = $name;

        $this->client->updateRequestData($data);
    }

    /**
     * @When I describe it as :description in :localeCode
     */
    public function iDescribeItAsIn(string $description, string $localeCode): void
    {
        $data = ['translations' => [$localeCode => ['locale' => $localeCode]]];
        $data['translations'][$localeCode]['description'] = $description;

        $this->client->updateRequestData($data);
    }

    /**
     * @When /^I define it for the (zone named "[^"]+")$/
     */
    public function iDefineItForTheZone(ZoneInterface $zone): void
    {
        $this->client->addRequestData('zone', $this->iriConverter->getIriFromItem($zone));
    }

    /**
     * @When I make it available in channel :channel
     */
    public function iMakeItAvailableInChannel(ChannelInterface $channel): void
    {
        $this->client->addRequestData('channels', [$this->iriConverter->getIriFromItem($channel)]);
    }

    /**
     * @When I choose :shippingCalculator calculator
     */
    public function iChooseCalculator(string $shippingCalculator): void
    {
        $this->client->addRequestData('calculator', $shippingCalculator);
    }

    /**
     * @When I specify its amount as :amount for :channel channel
     */
    public function iSpecifyItsAmountAsForChannel(ChannelInterface $channel, int $amount): void
    {
        $this->client->addRequestData('configuration', [$channel->getCode() => ['amount' => $amount]]);
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
     * @Then the shipping method :shippingMethodName should appear in the registry
     */
    public function theShippingMethodShouldAppearInTheRegistry(string $shippingMethodName): void
    {
        Assert::true(
            $this->responseChecker->hasItemWithTranslation($this->client->index(), 'en_US', 'name', $shippingMethodName),
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
            $this->responseChecker->hasItemWithTranslation($this->client->index(), 'en_US', 'name', $shippingMethodName),
            sprintf('Shipping method with name %s does not exists', $shippingMethodName)
        );
    }

    /**
     * @Then I should be notified that it has been successfully created
     */
    public function iShouldBeNotifiedThatItHasBeenSuccessfullyCreated(): void
    {
        Assert::true(
            $this->responseChecker->isCreationSuccessful($this->client->getLastResponse()),
            'Shipping method could not be created'
        );
    }

    /**
     * @Then the shipping method :shippingMethod should be available in channel :channel
     */
    public function theShippingMethodShouldBeAvailableInChannel(ShippingMethodInterface $shippingMethod, ChannelInterface $channel): void
    {
        Assert::true(
            $this->responseChecker->hasValueInCollection(
                $this->client->show($shippingMethod->getCode()),
                'channels',
                $this->iriConverter->getIriFromItem($channel)
            ),
            sprintf('Shipping method is not assigned to %s channel', $channel->getName())
        );
    }
}
