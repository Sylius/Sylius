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

namespace Sylius\Behat\Context\Ui\Shop;

use Behat\Behat\Context\Context;
use Sylius\Behat\Page\Shop\HomePageInterface;
use Sylius\Component\Locale\Context\LocaleNotFoundException;
use Webmozart\Assert\Assert;

final class LocaleContext implements Context
{
    /**
     * @var HomePageInterface
     */
    private $homePage;

    /**
     * @param HomePageInterface $homePage
     */
    public function __construct(HomePageInterface $homePage)
    {
        $this->homePage = $homePage;
    }

    /**
     * @Given I switched the shop's locale to :locale
     * @When I switch to the :locale locale
     * @When I change my locale to :locale
     */
    public function iSwitchTheLocaleToTheLocale($locale)
    {
        $this->homePage->open();
        $this->homePage->switchLocale($locale);
    }

    /**
     * @Then I should shop using the :locale locale
     * @Then I should still shop using the :locale locale
     */
    public function iShouldShopUsingTheLocale($locale)
    {
        Assert::same($this->homePage->getActiveLocale(), $locale);
    }

    /**
     * @Then I should be able to shop using the :locale locale
     * @Then the store should be available in the :locale locale
     */
    public function iShouldBeAbleToShopUsingTheLocale($locale)
    {
        Assert::oneOf($locale, $this->homePage->getAvailableLocales());
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
                implode('", "', $this->homePage->getAvailableLocales())
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
        } catch (LocaleNotFoundException $e) {
        }
    }
}
