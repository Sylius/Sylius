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
use Sylius\Component\Locale\Model\LocaleInterface;
use Webmozart\Assert\Assert;

final class LocaleContext implements Context
{
    public function __construct(
        private ApiClientInterface $client,
        private ResponseCheckerInterface $responseChecker,
        private SharedStorageInterface $sharedStorage,
    ) {
    }

    /**
     * @When I get available locales
     */
    public function iGetAvailableLocales(): void
    {
        $this->client->index(Resources::LOCALES);
    }

    /**
     * @When I get :locale locale
     */
    public function iGetLocale(LocaleInterface $locale): void
    {
        $this->client->show(Resources::LOCALES, $locale->getCode());
    }

    /**
     * @When I switch to the :localeCode locale
     * @When I use the locale :localeCode
     */
    public function iSwitchToTheLocale(string $localeCode): void
    {
        $this->sharedStorage->set('current_locale_code', $localeCode);
    }

    /**
     * @Then I should have :count locales
     */
    public function iShouldHaveLocales(int $count): void
    {
        Assert::same(
            $this->responseChecker->countCollectionItems($this->client->getLastResponse()),
            $count,
        );
    }

    /**
     * @Then the :name locale with code :code should be available
     */
    public function theLocaleWithCodeShouldBeAvailable(string $name, string $code): void
    {
        Assert::true(
            $this->responseChecker->hasItemWithValues($this->client->getLastResponse(), ['name' => $name, 'code' => $code]),
        );
    }

    /**
     * @Then the :name locale with code :code should not be available
     */
    public function theLocaleWithCodeShouldNotBeAvailable(string $name, string $code): void
    {
        Assert::false(
            $this->responseChecker->hasItemWithValues($this->client->getLastResponse(), ['name' => $name, 'code' => $code]),
        );
    }

    /**
     * @Then I should have :name with code :code
     */
    public function iShouldHaveWithCode(string $name, string $code): void
    {
        $response = $this->client->getLastResponse();

        Assert::true($this->responseChecker->hasValue($response, 'name', $name));
        Assert::true($this->responseChecker->hasValue($response, 'code', $code));
    }

    /**
     * @Then I should( still) shop using the :localeCode locale
     */
    public function iShouldShopUsingTheLocale(string $localeCode): void
    {
        $this->client->buildCreateRequest(Resources::ORDERS);

        Assert::same($this->responseChecker->getValue($this->client->create(), 'localeCode'), $localeCode);
    }

    /**
     * @Then I should be able to shop using the :localeCode locale
     */
    public function iShouldBeAbleToShopUsingTheLocale(string $localeCode): void
    {
        $this->iGetAvailableLocales();

        Assert::true(
            $this->responseChecker->hasItemWithValue($this->client->getLastResponse(), 'code', $localeCode),
        );
    }

    /**
     * @Then I should not be able to shop using the :localeCode locale
     */
    public function iShouldNotBeAbleToShopUsingTheLocale(string $localeCode): void
    {
        $this->iGetAvailableLocales();

        Assert::false(
            $this->responseChecker->hasItemWithValue($this->client->getLastResponse(), 'code', $localeCode),
        );
    }
}
