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
use Behat\Hook\AfterScenario;
use Behat\Step\Given;
use Behat\Step\Then;
use Sylius\Behat\Page\Admin\DashboardPageInterface;
use Symfony\Component\HttpKernel\KernelInterface;
use Webmozart\Assert\Assert;

final class NavbarNotificationsContext implements Context
{
    private const NOTIFICATIONS_ENABLED_ENV = 'TEST_SYLIUS_ADMIN_NOTIFICATIONS_ENABLED';

    public function __construct(
        private readonly DashboardPageInterface $dashboardPage,
        private readonly KernelInterface $kernel,
    ) {
    }

    #[Given('the admin notifications are disabled')]
    public function theAdminNotificationsAreDisabled(): void
    {
        $this->overrideNotificationsEnabledEnv('0');
    }

    #[AfterScenario('@notifications_disabled')]
    public function restoreNotificationsEnabledEnv(): void
    {
        $this->overrideNotificationsEnabledEnv(null);
    }

    #[Then('I should see the notifications icon in the navbar')]
    public function iShouldSeeTheNotificationsIconInTheNavbar(): void
    {
        Assert::true($this->dashboardPage->hasNotificationsIcon());
    }

    #[Then('I should not see the notifications icon in the navbar')]
    public function iShouldNotSeeTheNotificationsIconInTheNavbar(): void
    {
        Assert::false($this->dashboardPage->hasNotificationsIcon());
    }

    /**
     * Overrides the notifications environment variable in the current process and reboots the kernel,
     * so that the Symfony session serves the next request with a fresh container re-reading the configuration.
     */
    private function overrideNotificationsEnabledEnv(?string $value): void
    {
        if (null === $value) {
            putenv(self::NOTIFICATIONS_ENABLED_ENV);
            unset($_ENV[self::NOTIFICATIONS_ENABLED_ENV], $_SERVER[self::NOTIFICATIONS_ENABLED_ENV]);
        } else {
            putenv(self::NOTIFICATIONS_ENABLED_ENV . '=' . $value);
            $_ENV[self::NOTIFICATIONS_ENABLED_ENV] = $value;
            $_SERVER[self::NOTIFICATIONS_ENABLED_ENV] = $value;
        }

        $this->kernel->reboot(null);
    }
}
