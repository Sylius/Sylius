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

namespace Sylius\Behat\Context\Api\Admin;

use ApiPlatform\Api\IriConverterInterface;
use Behat\Behat\Context\Context;
use Sylius\Behat\Client\ApiClientInterface;
use Sylius\Behat\Client\ResponseCheckerInterface;
use Sylius\Behat\Context\Api\Resources;
use Sylius\Component\Addressing\Model\CountryInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ShopBillingDataInterface;
use Webmozart\Assert\Assert;

final class ManagingChannelsBillingDataContext implements Context
{
    public function __construct(
        private ApiClientInterface $client,
        private ResponseCheckerInterface $responseChecker,
        private IriConverterInterface $iriConverter,
    ) {
    }

    /**
     * @Then /^(this channel) company should be "([^"]+)"$/
     */
    public function thisChannelCompanyShouldBe(ChannelInterface $channel, string $company): void
    {
        $shopBillingData = $this->getShopBillingDataFromChannel($channel);

        Assert::same($shopBillingData->getCompany(), $company);
    }

    /**
     * @Then /^(this channel) tax ID should be "([^"]+)"$/
     */
    public function thisChanneTaxIdShouldBe(ChannelInterface $channel, string $taxId): void
    {
        $shopBillingData = $this->getShopBillingDataFromChannel($channel);

        Assert::same($shopBillingData->getTaxId(), $taxId);
    }

    /**
     * @Then /^(this channel) shop billing address should be "([^"]+)", "([^"]+)" "([^"]+)" and ("([^"]+)" country)$/
     * @Then /^(this channel) shop billing address should still be "([^"]+)", "([^"]+)" "([^"]+)" and ("([^"]+)" country)$/
     */
    public function thisChannelShopBillingAddressShouldBe(
        ChannelInterface $channel,
        string $street,
        string $postcode,
        string $city,
        CountryInterface $country,
    ): void {
        $shopBillingData = $this->getShopBillingDataFromChannel($channel);

        Assert::same($shopBillingData->getStreet(), $street);
        Assert::same($shopBillingData->getPostcode(), $postcode);
        Assert::same($shopBillingData->getCity(), $city);
        Assert::same($shopBillingData->getCountryCode(), $country->getCode());
    }

    private function getShopBillingDataFromChannel(ChannelInterface $channel): ShopBillingDataInterface
    {
        $this->client->show(Resources::CHANNELS, $channel->getCode());

        /** @var ShopBillingDataInterface $shopBillingData */
        $shopBillingData = $this->iriConverter->getResourceFromIri($this->responseChecker->getValue($this->client->getLastResponse(), 'shopBillingData'));

        return $shopBillingData;
    }
}
