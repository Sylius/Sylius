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

use Behat\Behat\Context\Context;
use Sylius\Behat\Client\ApiClientInterface;
use Sylius\Behat\Client\ResponseCheckerInterface;
use Sylius\Behat\Context\Api\NormalizedKeyAwareTrait;
use Sylius\Behat\Context\Api\Resources;
use Sylius\Component\Addressing\Model\CountryInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Symfony\Component\Serializer\NameConverter\NameConverterInterface;
use Webmozart\Assert\Assert;

final readonly class ManagingChannelsBillingDataContext implements Context
{
    use NormalizedKeyAwareTrait;

    public function __construct(
        private ApiClientInterface $client,
        private ResponseCheckerInterface $responseChecker,
        private ?NameConverterInterface $nameConverter,
    ) {
    }

    /**
     * @Then /^(this channel) company should be "([^"]+)"$/
     */
    public function thisChannelCompanyShouldBe(ChannelInterface $channel, string $company): void
    {
        $shopBillingData = $this->getShopBillingDataFromChannel($channel);

        Assert::same($shopBillingData[$this->getNormalizedKey('company')], $company);
    }

    /**
     * @Then /^(this channel) tax ID should be "([^"]+)"$/
     */
    public function thisChannelTaxIdShouldBe(ChannelInterface $channel, string $taxId): void
    {
        $shopBillingData = $this->getShopBillingDataFromChannel($channel);

        Assert::same($shopBillingData[$this->getNormalizedKey('taxId')], $taxId);
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

        Assert::same($shopBillingData[$this->getNormalizedKey('street')], $street);
        Assert::same($shopBillingData[$this->getNormalizedKey('postcode')], $postcode);
        Assert::same($shopBillingData[$this->getNormalizedKey('city')], $city);
        Assert::same($shopBillingData[$this->getNormalizedKey('countryCode')], $country->getCode());
    }

    /**
     * @return array<string, string>
     */
    private function getShopBillingDataFromChannel(ChannelInterface $channel): array
    {
        $response = $this->client->show(Resources::CHANNELS, $channel->getCode());

        return $this->responseChecker->getValue($response, 'shopBillingData');
    }
}
