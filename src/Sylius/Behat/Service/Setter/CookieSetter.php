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

final readonly class CookieSetter implements CookieSetterInterface
{
    public function __construct(
        private Session $minkSession,
        private \ArrayAccess $minkParameters,
    ) {
    }

    public function setCookie(string $name, string $value): void
    {
        $driver = $this->minkSession->getDriver();

        $this->ensureDriverStarted($driver);

        if ($driver instanceof SymfonyDriver) {
            $driver->getClient()->getCookieJar()->set(
                new Cookie($name, $value, null, null, parse_url($this->minkParameters['base_url'], \PHP_URL_HOST)),
            );

            return;
        }

        $this->prepareMinkSessionIfNeeded($this->minkSession);
        $this->minkSession->setCookie($name, $value);
    }

    private function ensureDriverStarted(mixed $driver): void
    {
        if (($driver instanceof ChromeDriver || $driver instanceof PantherDriver) && !$driver->isStarted()) {
            $driver->start();
        }
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

        if ($driver instanceof Selenium2Driver) {
            return $driver->getWebDriverSession() === null || $this->isPageNotLoaded($session->getCurrentUrl());
        }

        if ($driver instanceof ChromeDriver) {
            return $this->isPageNotLoaded($session->getCurrentUrl());
        }

        return !str_contains($session->getCurrentUrl(), $this->minkParameters['base_url']);
    }

    private function isPageNotLoaded(string $url): bool
    {
        return in_array($url, ['', 'about:blank', 'data:,'], true);
    }
}
