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
