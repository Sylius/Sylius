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

namespace Sylius\Behat\Context\Ui\Admin;

use Behat\Behat\Context\Context;
use Sylius\Behat\Page\Admin\Administrator\CreatePageInterface;
use Sylius\Behat\Page\Admin\DashboardPageInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Webmozart\Assert\Assert;

final class LocaleContext implements Context
{
    public function __construct(
        private DashboardPageInterface $dashboardPage,
        private TranslatorInterface $translator,
        private CreatePageInterface $createPage,
    ) {
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
     * @Then I should be notified that this email is not valid in :localeCode locale
     */
    public function iShouldBeNotifiedThatThisEmailIsNotValidInLocale(string $localeCode): void
    {
        Assert::same($this->createPage->getValidationMessage('email'), $this->translate('sylius.contact.email.invalid', $localeCode, 'validators'));
    }

    /**
     * @Then I should see sidebar catalog section configuration in :localeCode locale
     */
    public function iShouldSeeSidebarSectionConfigurationInLocale(string $localeCode): void
    {
        Assert::true($this->dashboardPage->isSectionWithLabelVisible($this->translate('sylius.menu.admin.main.catalog.header', $localeCode)));
    }

    private function translate(string $text, string $localeCode, ?string $domain = null): string
    {
        return $this->translator->trans($text, [], $domain, $localeCode);
    }
}
