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
use Sylius\Behat\Page\Admin\Administrator\UpdatePageInterface;
use Sylius\Behat\Page\Admin\DashboardPageInterface;
use Sylius\Component\Core\Model\AdminUserInterface;
use Symfony\Component\Translation\TranslatorInterface;
use Webmozart\Assert\Assert;

/**
 * @author Jan Góralski <jan.goralski@lakion.com>
 */
final class LocaleContext implements Context
{
    /**
     * @var UpdatePageInterface
     */
    private $adminUpdatePage;

    /**
     * @var DashboardPageInterface
     */
    private $dashboardPage;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @param UpdatePageInterface $adminUpdatePage
     * @param DashboardPageInterface $dashboardPage
     * @param TranslatorInterface $translator
     */
    public function __construct(
        UpdatePageInterface $adminUpdatePage,
        DashboardPageInterface $dashboardPage,
        TranslatorInterface $translator
    ) {
        $this->adminUpdatePage = $adminUpdatePage;
        $this->dashboardPage = $dashboardPage;
        $this->translator = $translator;
    }

    /**
     * @Then I should be viewing the panel in :localeCode
     * @Then I should still be viewing the panel in :localeCode
     * @Then they should be viewing the panel in :localeCode
     */
    public function iShouldBeViewingThePanelIn($localeCode)
    {
        $this->dashboardPage->open();

        $expectedSubHeader = $this->translate('sylius.ui.overview_of_your_store', $localeCode);
        $actualSubHeader = $this->dashboardPage->getSubHeader();

        Assert::same(
            $actualSubHeader,
            $expectedSubHeader,
            sprintf('Dashboard header should say "%s", but says "%s" instead.', $expectedSubHeader, $actualSubHeader)
        );
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
