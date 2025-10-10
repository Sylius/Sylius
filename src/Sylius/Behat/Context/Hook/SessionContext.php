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

namespace Sylius\Behat\Context\Hook;

use Behat\Behat\Context\Context;
use Behat\Mink\Mink;
use Symfony\Component\HttpFoundation\Exception\SessionNotFoundException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionFactoryInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

final class SessionContext implements Context
{
    public function __construct(
        private RequestStack $requestStack,
        private ?SessionFactoryInterface $sessionFactory = null,
        private ?Mink $mink = null,
    ) {
    }

    /**
     * @BeforeScenario @ui
     */
    public function startSession(): void
    {
        if (null === $this->sessionFactory) {
            return;
        }

        try {
            $this->requestStack->getSession();
        } catch (SessionNotFoundException) {
            $session = $this->sessionFactory->createSession();
            $session->start();
            $session->save();

            $request = $this->requestStack->getMainRequest();
            if (null !== $request) {
                $this->saveSessionOnRequest($request, $session);

                return;
            }

            $this->saveSessionOnNewRequest($session);
        }
    }

    /**
     * @AfterScenario @ui
     */
    public function stopBrowserSession(): void
    {
        if (null === $this->mink) {
            return;
        }

        // Stop sessions without full reset to preserve flash messages
        if ($this->mink->hasSession($this->mink->getDefaultSessionName())) {
            $session = $this->mink->getSession($this->mink->getDefaultSessionName());
            if ($session->isStarted()) {
                $session->stop();
            }
        }

        // Stop javascript session (panther) if it exists
        $javascriptSessionName = 'panther';
        if ($this->mink->hasSession($javascriptSessionName)) {
            $session = $this->mink->getSession($javascriptSessionName);
            if ($session->isStarted()) {
                $session->stop();
            }
        }
    }

    private function saveSessionOnNewRequest(SessionInterface $session): void
    {
        $request = new Request();
        $this->saveSessionOnRequest($request, $session);

        $this->requestStack->push($request);
    }

    private function saveSessionOnRequest(Request $request, SessionInterface $session): void
    {
        $request->setSession($session);
    }
}
