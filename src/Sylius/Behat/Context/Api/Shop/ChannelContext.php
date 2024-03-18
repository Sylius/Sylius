<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Behat\Context\Api\Shop;

use Behat\Behat\Context\Context;
use Sylius\Behat\Client\ApiClientInterface;
use Sylius\Behat\Client\ResponseCheckerInterface;
use Sylius\Behat\Context\Api\Resources;
use Sylius\Behat\Service\SharedStorageInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Webmozart\Assert\Assert;

final class ChannelContext implements Context
{
    public function __construct(
        private ApiClientInterface $client,
        private ResponseCheckerInterface $responseChecker,
        private SharedStorageInterface $sharedStorage,
        private string $apiUrlPrefix,
    ) {
    }

    /**
     * @When /^I (?:am browsing|start browsing|try to browse|browse) (?:|the )("[^"]+" channel)$/
     * @When /^I (?:start browsing|try to browse|browse) (that channel)$/
     * @When /^I (?:am browsing|start browsing|try to browse|browse) (?:|the )(channel "[^"]+")$/
     */
    public function iAmBrowsingChannel(ChannelInterface $channel): void
    {
        $this->sharedStorage->set('hostname', $channel->getHostname());
        $this->sharedStorage->remove('current_locale_code');

        $this->client->show(Resources::CHANNELS, $channel->getCode());
    }

    /**
     * @Then I should (still) shop using the :currencyCode currency
     */
    public function iShouldShopUsingTheCurrency(string $currencyCode): void
    {
        Assert::same(
            $this->responseChecker->getValue(
                $this->client->getLastResponse(),
                'baseCurrency',
            ),
            sprintf('%s/shop/currencies/%s', $this->apiUrlPrefix, $currencyCode),
        );
    }

    /**
     * @Then I should be able to shop using the :currencyCode currency
     */
    public function iShouldBeAbleToShopUsingTheCurrency(string $currencyCode): void
    {
        $this->client->index(Resources::CURRENCIES);

        Assert::true(
            $this->responseChecker->hasItemWithValue(
                $this->client->getLastResponse(),
                'code',
                $currencyCode,
            ),
        );
    }

    /**
     * @Then I should not be able to shop using the :currencyCode currency
     */
    public function iShouldNotBeAbleToShopUsingTheCurrency(string $currencyCode): void
    {
        $this->client->index(Resources::CURRENCIES);

        Assert::false(
            $this->responseChecker->hasItemWithValue(
                $this->client->getLastResponse(),
                'code',
                $currencyCode,
            ),
        );
    }
}
