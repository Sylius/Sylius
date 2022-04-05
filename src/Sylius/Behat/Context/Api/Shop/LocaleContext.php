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
    private $localesClient;

    /** @var ApiClientInterface */
    private $cartsClient;

    /** @var ResponseCheckerInterface */
    private $responseChecker;

    public function __construct(ApiClientInterface $localesClient, ApiClientInterface $cartsClient, ResponseCheckerInterface $responseChecker)
    {
        $this->localesClient = $localesClient;
        $this->cartsClient = $cartsClient;
        $this->responseChecker = $responseChecker;
    }

    /**
     * @When I get available locales
     */
    public function iGetAvailableLocales(): void
    {
        $this->localesClient->index();
    }

    /**
     * @When I get :locale locale
     */
    public function iGetLocale(LocaleInterface $locale): void
    {
        $this->localesClient->show($locale->getCode());
    }

    /**
     * @Then I should have :count locales
     */
    public function iShouldHaveLocales(int $count): void
    {
        Assert::same(
            $this->responseChecker->countCollectionItems($this->localesClient->getLastResponse()),
            $count
        );
    }

    /**
     * @Then the :name locale with code :code should be available
     */
    public function theLocaleWithCodeShouldBeAvailable(string $name, string $code): void
    {
        Assert::true(
            $this->responseChecker->hasItemWithValues($this->localesClient->getLastResponse(), ['name' => $name, 'code' => $code])
        );
    }

    /**
     * @Then the :name locale with code :code should not be available
     */
    public function theLocaleWithCodeShouldNotBeAvailable(string $name, string $code): void
    {
        Assert::false(
            $this->responseChecker->hasItemWithValues($this->localesClient->getLastResponse(), ['name' => $name, 'code' => $code])
        );
    }

    /**
     * @Then I should have :name with code :code
     */
    public function iShouldHaveWithCode(string $name, string $code): void
    {
        $response = $this->localesClient->getLastResponse();

        Assert::true($this->responseChecker->hasValue($response, 'name', $name));
        Assert::true($this->responseChecker->hasValue($response, 'code', $code));
    }

    /**
     * @Then I should( still) shop using the :localeCode locale
     */
    public function iShouldShopUsingTheLocale(string $localeCode): void
    {
        $this->cartsClient->buildCreateRequest();

        Assert::same(
            $this->responseChecker->getValue($this->cartsClient->create(), 'localeCode'),
            $localeCode
        );
    }
}
