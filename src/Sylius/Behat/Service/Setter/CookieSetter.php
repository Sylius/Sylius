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

namespace Sylius\Behat\Service\Setter;

use Behat\Mink\Session;
use FriendsOfBehat\SymfonyExtension\Driver\SymfonyDriver;
use Symfony\Component\BrowserKit\Cookie;

final class CookieSetter implements CookieSetterInterface
{
    /**
     * @var Session
     */
    private $minkSession;

    /**
     * @var array
     */
    private $minkParameters;

    /**
     * @param Session $minkSession
     * @param array $minkParameters
     */
    public function __construct(Session $minkSession, array $minkParameters)
    {
        $this->minkSession = $minkSession;
        $this->minkParameters = $minkParameters;
    }

    /**
     * {@inheritdoc}
     */
    public function setCookie($name, $value)
    {
        $this->prepareMinkSessionIfNeeded();

        $driver = $this->minkSession->getDriver();

        if ($driver instanceof SymfonyDriver) {
            $driver->getClient()->getCookieJar()->set(
                new Cookie($name, $value, null, null, parse_url($this->minkParameters['base_url'], PHP_URL_HOST))
            );

            return;
        }

        $this->minkSession->setCookie($name, $value);
    }

    private function prepareMinkSessionIfNeeded()
    {
        if ($this->minkSession->getDriver() instanceof SymfonyDriver) {
            return;
        }

        if (false !== strpos($this->minkSession->getCurrentUrl(), $this->minkParameters['base_url'])) {
            return;
        }

        $this->minkSession->visit(rtrim($this->minkParameters['base_url'], '/') . '/');
    }
}
