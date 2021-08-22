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
use Sylius\Component\Promotion\Event\CatalogPromotionCreated;
use Symfony\Component\Messenger\MessageBus;
use Symfony\Component\Messenger\MessageBusInterface;
use Webmozart\Assert\Assert;

final class ManagingCatalogPromotionsContext implements Context
{
    private ApiClientInterface $client;
    private ResponseCheckerInterface $responseChecker;
    private MessageBusInterface $messageBus;

    public function __construct(
        ApiClientInterface $client,
        ResponseCheckerInterface $responseChecker,
        MessageBusInterface $messageBus
    ) {
        $this->client = $client;
        $this->responseChecker = $responseChecker;
        $this->messageBus = $messageBus;
    }

    /**
     * @When I create a new catalog promotion with :code code and :name name
     */
    public function iCreateANewCatalogPromotionWithCodeAndName(string $code, string $name): void
    {
        $this->client->buildCreateRequest();
        $this->client->addRequestData('code', $code);
        $this->client->addRequestData('name', $name);
        $this->client->create();
    }

    /**
     * @Then there should be :amount new catalog promotion on the list
     */
    public function thereShouldBeNewCatalogPromotionOnTheList(int $amount): void
    {
        Assert::count($this->responseChecker->getCollection($this->client->index()), $amount);
    }

    /**
     * @Then it should have :code code and :name name
     */
    public function itShouldHaveCodeAndName(string $code, string $name): void
    {
        Assert::true(
            $this->responseChecker->hasItemWithValues($this->client->index(), ['code' => $code, 'name' => $name]),
            sprintf('Cannot find catalog promotions with code "%s" and name "%s" in the list', $code, $name)
        );
    }

    /**
     * @Then this catalog promotion should be usable
     */
    public function thisCatalogPromotionShouldBeUsable(): void
    {
        Assert::isInstanceOf($this->messageBus->getDispatchedMessages()[0]['message'], CatalogPromotionCreated::class);
    }
}
