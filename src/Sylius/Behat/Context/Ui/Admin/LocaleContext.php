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

final readonly class LocaleContext implements Context
{
    public function __construct(
        private DashboardPageInterface $dashboardPage,
        private TranslatorInterface $translator,
        private CreatePageInterface $createPage,
    ) {
    }

    /**
     * @Then I should be viewing the administration panel in :localeCode locale
     * @Then I should still be viewing the administration panel in :localeCode locale
     * @Then they should be viewing the administration panel in :localeCode locale
     */
    public function iShouldBeViewingTheAdministrationPanelIn(string $localeCode): void
    {
        if (!$this->dashboardPage->isOpen()) {
            $this->dashboardPage->open();
        }

        Assert::same($this->dashboardPage->getDashboardHeader(), $this->translate('sylius.ui.dashboard', $localeCode));
    }

    /**
     * @Then I should be notified that this email is not valid in :localeCode locale
     */
    public function iShouldBeNotifiedThatThisEmailIsNotValidInLocale(string $localeCode): void
    {
        Assert::same($this->createPage->getValidationMessage('field_email'), $this->translate('sylius.contact.email.invalid', $localeCode, 'validators'));
    }

    private function translate(string $text, string $localeCode, ?string $domain = null): string
    {
        return $this->translator->trans($text, [], $domain, $localeCode);
    }
}
