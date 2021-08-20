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

namespace Sylius\Behat\Context\Application\Admin;

use Behat\Behat\Context\Context;
use Sylius\Behat\Client\ApiClientInterface;
use Sylius\Component\Promotion\Message\CatalogPromotionCreated;
use Symfony\Component\Messenger\MessageBus;
use Webmozart\Assert\Assert;

final class ManagingCatalogPromotionsContext implements Context
{
    private ApiClientInterface $client;
    private MessageBus $messageBus;

    public function __construct(ApiClientInterface $client, MessageBus $messageBus)
    {
        $this->client = $client;
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
        Assert::count($this->messageBus->getDispatchedMessages(), $amount);//todo
    }

    /**
     * @Then it should have :code code and :name name
     */
    public function itShouldHaveCodeAndName(string $code, string $name): void
    {
        /** @var CatalogPromotionCreated $actualMessage */
        $actualMessage = $this->messageBus->getDispatchedMessages()[0];
        Assert::same($actualMessage->code, $code);
        Assert::same($actualMessage->name, $name);
    }
}
