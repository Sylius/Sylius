<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Context\Ui\Admin;

use Behat\Behat\Context\Context;
use Sylius\Behat\Page\Admin\DashboardPageInterface;
use Symfony\Component\Translation\TranslatorInterface;
use Webmozart\Assert\Assert;

/**
 * @author Jan Góralski <jan.goralski@lakion.com>
 */
final class LocaleContext implements Context
{
    /**
     * @var DashboardPageInterface
     */
    private $dashboardPage;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @param DashboardPageInterface $dashboardPage
     * @param TranslatorInterface $translator
     */
    public function __construct(DashboardPageInterface $dashboardPage, TranslatorInterface $translator)
    {
        $this->dashboardPage = $dashboardPage;
        $this->translator = $translator;
    }

    /**
     * @Then I should be viewing the administration panel in :localeCode
     * @Then I should still be viewing the administration panel in :localeCode
     * @Then they should be viewing the administration panel in :localeCode
     */
    public function iShouldBeViewingTheAdministrationPanelIn($localeCode)
    {
        $this->dashboardPage->open();

        Assert::same($this->dashboardPage->getSubHeader(), $this->translate('sylius.ui.overview_of_your_store', $localeCode));
    }

    /**
     * @param string $text
     * @param string $localeCode
     *
     * @return string
     */
    private function translate($text, $localeCode)
    {
        $this->translator->setLocale($localeCode);

        return $this->translator->trans($text);
    }
}
