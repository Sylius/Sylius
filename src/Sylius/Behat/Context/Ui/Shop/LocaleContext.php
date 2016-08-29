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
     * @When I switch to the :localeName locale
     * @When I change my locale to :localeName
     */
    public function iSwitchTheLocaleToTheLocale($localeName)
    {
        $this->homePage->open();
        $this->homePage->switchLocale($localeName);
    }

    /**
     * @Then I should (still) shop using the :localeName locale
     */
    public function iShouldShopUsingTheLocale($localeName)
    {
        $this->homePage->open();

        Assert::same($localeName, $this->homePage->getActiveLocale());
    }

    /**
     * @Then I should be able to shop using the :localeName locale
     */
    public function iShouldBeAbleToShopUsingTheLocale($localeName)
    {
        $this->homePage->open();

        Assert::oneOf($localeName, $this->homePage->getAvailableLocales());
    }

    /**
     * @Then I should not be able to shop using the :localeName locale
     */
    public function iShouldNotBeAbleToShopUsingTheLocale($localeName)
    {
        $this->homePage->open();

        if (in_array($localeName, $this->homePage->getAvailableLocales(), true)) {
            throw new \InvalidArgumentException(sprintf(
                'Expected "%s" not to be in "%s"',
                $localeName,
                implode('", "', $this->homePage->getAvailableLocales())
            ));
        }
    }

    /**
     * @Then I should not be able to shop
     */
    public function iShouldNotBeAbleToShop()
    {
        $this->homePage->tryToOpen();

        Assert::false($this->homePage->isOpen(), 'Homepage should not be opened!');
    }
}
