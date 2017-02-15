<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Context\Ui\Shop;

use Behat\Behat\Context\Context;
use Sylius\Behat\Page\Shop\HomePageInterface;
use Sylius\Component\Locale\Context\LocaleNotFoundException;
use Webmozart\Assert\Assert;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
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
     * @Given I switched the shop's locale to :localName
     * @When I switch to the :localeName locale
     * @When I change my locale to :localeName
     */
    public function iSwitchTheLocaleToTheLocale($localeName)
    {
        $this->homePage->open();
        $this->homePage->switchLocale($localeName);
    }

    /**
     * @Then I should shop using the :localeName locale
     * @Then I should still shop using the :localeName locale
     */
    public function iShouldShopUsingTheLocale($localeName)
    {
        Assert::same($this->homePage->getActiveLocale(), $localeName);
    }

    /**
     * @Then I should be able to shop using the :localeName locale
     * @Then the store should be available in the :localName locale
     */
    public function iShouldBeAbleToShopUsingTheLocale($localeName)
    {
        Assert::oneOf($localeName, $this->homePage->getAvailableLocales());
    }

    /**
     * @Then I should not be able to shop using the :localeName locale
     * @Then the store should not be available in the :localName locale
     */
    public function iShouldNotBeAbleToShopUsingTheLocale($localeName)
    {
        if (in_array($localeName, $this->homePage->getAvailableLocales(), true)) {
            throw new \InvalidArgumentException(sprintf(
                'Expected "%s" not to be in "%s"',
                $localeName,
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
