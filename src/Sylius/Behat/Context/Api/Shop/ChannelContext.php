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

namespace Sylius\Behat\Context\Api\Shop;

use Behat\Behat\Context\Context;
use Sylius\Behat\Client\ApiClientInterface;
use Sylius\Behat\Client\ResponseCheckerInterface;
use Sylius\Behat\Service\SharedStorageInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Webmozart\Assert\Assert;

final class ChannelContext implements Context
{
    /** @var ApiClientInterface */
    private $client;

    /** @var ResponseCheckerInterface */
    private $responseChecker;

    /** @var SharedStorageInterface */
    private $sharedStorage;

    public function __construct(
        ApiClientInterface $client,
        ResponseCheckerInterface $responseChecker,
        SharedStorageInterface $sharedStorage
    ) {
        $this->client = $client;
        $this->responseChecker = $responseChecker;
        $this->sharedStorage = $sharedStorage;
    }

    /**
     * @Given I am browsing channel :channel
     */
    public function iAmBrowsingChannel(ChannelInterface $channel): void
    {
        $this->sharedStorage->set('hostname', $channel->getHostname());
    }

    /**
     * @When /^I (?:am browsing|start browsing|try to browse|browse) (that channel)$/
     */
    public function iVisitChannelHomepage(ChannelInterface $channel): void
    {
        $this->client->show($channel->getCode());
    }

    /**
     * @Then I should (still) shop using the :currencyCode currency
     */
    public function iShouldShopUsingTheCurrency(string $currencyCode): void
    {
        Assert::same($this->responseChecker->getValue(
            $this->client->getLastResponse(), 'baseCurrency')['code'],
            $currencyCode
        );
    }
}
