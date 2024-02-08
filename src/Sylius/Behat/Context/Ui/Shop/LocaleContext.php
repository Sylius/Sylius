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

namespace Sylius\Behat\Context\Ui\Shop;

use Behat\Behat\Context\Context;
use Sylius\Behat\Page\Shop\HomePageInterface;
use Sylius\Behat\Service\SharedStorageInterface;
use Sylius\Component\Locale\Context\LocaleNotFoundException;
use Sylius\Component\Locale\Model\LocaleInterface;
use Webmozart\Assert\Assert;

final class LocaleContext implements Context
{
    public function __construct(
        private HomePageInterface $homePage,
        private SharedStorageInterface $sharedStorage,
    ) {
    }

    /**
     * @Given I switched the shop's locale to :locale
     * @Given I have switched to the :locale locale
     * @When I switch to the :locale locale
     * @When I change my locale to :locale
     */
    public function iSwitchTheLocaleToTheLocale(LocaleInterface $locale): void
    {
        $this->homePage->open();
        $this->homePage->switchLocale($locale->getName('en_US'));

        $this->sharedStorage->set('current_locale_code', $locale->getCode());
    }

    /**
     * @When I use the locale :localeCode
     */
    public function iUseTheLocale(string $localeCode): void
    {
        $this->homePage->tryToOpen(['_locale' => $localeCode]);
    }

    /**
     * @Then I should shop using the :localeNameInItsLocale locale
     * @Then I should still shop using the :localeNameInItsLocale locale
     */
    public function iShouldShopUsingTheLocale(string $localeNameInItsLocale): void
    {
        Assert::same($this->homePage->getActiveLocale(), $localeNameInItsLocale);
    }

    /**
     * @Then I should be able to shop using the :localeNameInCurrentLocale locale
     * @Then the store should be available in the :localeNameInCurrentLocale locale
     */
    public function iShouldBeAbleToShopUsingTheLocale(string $localeNameInCurrentLocale)
    {
        Assert::oneOf($localeNameInCurrentLocale, $this->homePage->getAvailableLocales());
    }

    /**
     * @Then I should not be able to shop using the :locale locale
     * @Then the store should not be available in the :locale locale
     */
    public function iShouldNotBeAbleToShopUsingTheLocale($locale)
    {
        if (in_array($locale, $this->homePage->getAvailableLocales(), true)) {
            throw new \InvalidArgumentException(sprintf(
                'Expected "%s" not to be in "%s"',
                $locale,
                implode('", "', $this->homePage->getAvailableLocales()),
            ));
        }
    }

    /**
     * @Then I should not be able to shop without default locale
     */
    public function iShouldNotBeAbleToShop()
    {
        try {
            $this->homePage->tryToOpen();

            throw new \Exception('The page should not be able to open.');
        } catch (LocaleNotFoundException) {
        }
    }
}
