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

namespace Sylius\Behat\Service\Setter;

use Behat\Mink\Driver\PantherDriver;
use Behat\Mink\Driver\Selenium2Driver;
use Behat\Mink\Session;
use DMore\ChromeDriver\ChromeDriver;
use FriendsOfBehat\SymfonyExtension\Driver\SymfonyDriver;
use Symfony\Component\BrowserKit\Cookie;

final class CookieSetter implements CookieSetterInterface
{
    public function __construct(
        private Session $minkSession,
        private \ArrayAccess $minkParameters,
    ) {
    }

    public function setCookie($name, $value)
    {
        $driver = $this->minkSession->getDriver();

        if ($driver instanceof ChromeDriver || $driver instanceof PantherDriver) {
            if (!$driver->isStarted()) {
                $driver->start();
            }
        }

        $this->prepareMinkSessionIfNeeded($this->minkSession);

        if ($driver instanceof SymfonyDriver) {
            $driver->getClient()->getCookieJar()->set(
                new Cookie($name, $value, null, null, parse_url($this->minkParameters['base_url'], \PHP_URL_HOST)),
            );

            return;
        }

        $this->minkSession->setCookie($name, $value);
    }

    private function prepareMinkSessionIfNeeded(Session $session): void
    {
        if ($this->shouldMinkSessionBePrepared($session)) {
            $session->visit(rtrim($this->minkParameters['base_url'], '/') . '/');
        }
    }

    private function shouldMinkSessionBePrepared(Session $session): bool
    {
        $driver = $session->getDriver();

        if ($driver instanceof SymfonyDriver) {
            return false;
        }

        if ($driver instanceof Selenium2Driver && $driver->getWebDriverSession() === null) {
            return true;
        }

        if ($driver instanceof ChromeDriver) {
            return true;
        }

        if (str_contains($session->getCurrentUrl(), $this->minkParameters['base_url'])) {
            return false;
        }

        return true;
    }
}
