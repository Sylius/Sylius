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
use Sylius\Component\Locale\Model\LocaleInterface;
use Webmozart\Assert\Assert;

final class LocaleContext implements Context
{
    /** @var ApiClientInterface */
    private $localeClient;

    /** @var ApiClientInterface */
    private $channelClient;

    /** @var ResponseCheckerInterface */
    private $responseChecker;

    public function __construct(ApiClientInterface $localeClient, ApiClientInterface $channelClient, ResponseCheckerInterface $responseChecker)
    {
        $this->localeClient = $localeClient;
        $this->channelClient = $channelClient;
        $this->responseChecker = $responseChecker;
    }

    /**
     * @When I get available locales
     */
    public function iGetAvailableLocales(): void
    {
       $this->localeClient->index();
    }

    /**
     * @When I get :locale locale
     */
    public function iGetLocale(LocaleInterface $locale): void
    {
        $this->localeClient->show($locale->getCode());
    }

    /**
     * @Then I should have :count locales
     */
    public function iShouldHaveLocales(int $count): void
    {
        Assert::same(
            $this->responseChecker->countCollectionItems($this->localeClient->getLastResponse()),
            $count
        );
    }

    /**
     * @Then the :name locale with code :code should be available
     */
    public function theLocaleWithCodeShouldBeAvailable(string $name, string $code): void
    {
        Assert::true(
            $this->responseChecker->hasItemWithValues($this->localeClient->getLastResponse(), ['name' => $name, 'code' => $code])
        );
    }

    /**
     * @Then the :name locale with code :code should not be available
     */
    public function theLocaleWithCodeShouldNotBeAvailable(string $name, string $code): void
    {
        Assert::false(
            $this->responseChecker->hasItemWithValues($this->localeClient->getLastResponse(), ['name' => $name, 'code' => $code])
        );
    }

    /**
     * @Then I should have :name with code :code
     */
    public function iShouldHaveWithCode(string $name, string $code): void
    {
        $response = $this->localeClient->getLastResponse();

        Assert::true($this->responseChecker->hasValue($response, 'name', $name));
        Assert::true($this->responseChecker->hasValue($response, 'code', $code));
    }

    /**
     * @Then I should be informed that :rawLocaleName locale is default one
     */
    public function iShouldShopUsingTheLocale(string $rawLocaleName): void
    {
        $channelResponse = $this->channelClient->getLastResponse();

        $this->localeClient->showByIri($this->responseChecker->getValue($channelResponse, 'defaultLocale'));

        $localeResponse = $this->localeClient->getLastResponse();

        Assert::same($this->responseChecker->getValue($localeResponse, 'name'), $rawLocaleName);
    }

    /**
     * @Then I should be able to shop using the :rawLocaleName locale
     */
    public function iShouldBeAbleToShopUsingTheLocale(string $rawLocaleName): void
    {
        $this->assertAmountOfLocalesWithGivenNameInCollection($rawLocaleName, 1);
    }

    /**
     * @Then I should not be able to shop using the :rawLocaleName locale
     */
    public function iShouldNotBeAbleToShopUsingTheLocale($rawLocaleName): void
    {
        $this->assertAmountOfLocalesWithGivenNameInCollection($rawLocaleName, 0);
    }

    private function assertAmountOfLocalesWithGivenNameInCollection(string $rawLocaleName, int $amount): void
    {
        $this->localeClient->index();

        $localeResponse = $this->localeClient->getLastResponse();

        Assert::count(
            $this->responseChecker->getCollectionItemsWithValue($localeResponse, 'name', $rawLocaleName),
            $amount
        );
    }
}
