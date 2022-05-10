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

namespace Sylius\Behat\Service;

use Behat\Mink\Mink;
use Symfony\Component\Security\Core\Exception\TokenNotFoundException;

final class SessionManager implements SessionManagerInterface
{
    private const SESSION_CHROME_HEADLESS_SECOND = 'chrome_headless_second_session';

    private Mink $mink;

    private SharedStorageInterface $sharedStorage;

    private SecurityServiceInterface $securityService;

    public function __construct(Mink $mink, SharedStorageInterface $sharedStorage, SecurityServiceInterface $securityService)
    {
        $this->mink = $mink;
        $this->sharedStorage = $sharedStorage;
        $this->securityService = $securityService;
    }

    public function changeSession(): void
    {
        $sessionName = self::SESSION_CHROME_HEADLESS_SECOND;

        $this->saveAndRestartSession($sessionName);

        if ($this->sharedStorage->has($this->getKeyForToken($sessionName))) {
            $this->securityService->restoreToken($this->sharedStorage->get($this->getKeyForToken($sessionName)));
        }
    }

    public function restorePreviousSession(): void
    {
        if (!$this->sharedStorage->has('behat_previous_session_name')) {
            return;
        }

        /** @var string $sessionName */
        $sessionName = $this->sharedStorage->get('behat_previous_session_name');

        $this->saveAndRestartSession($sessionName);

        if ($this->sharedStorage->has($this->getKeyForToken($sessionName))) {
            $this->securityService->restoreToken($this->sharedStorage->get($this->getKeyForToken($sessionName)));
        }
    }

    private function saveAndRestartSession(string $newSessionName): void
    {
        /** @var string $previousSessionName */
        $previousSessionName = $this->mink->getDefaultSessionName();

        $this->sharedStorage->set('behat_previous_session_name', $previousSessionName);

        try {
            $token = $this->securityService->getCurrentToken();
            $this->sharedStorage->set($this->getKeyForToken($previousSessionName), $token);
        } catch (TokenNotFoundException $exception) {
        }

        $this->mink->setDefaultSessionName($newSessionName);

        $this->mink->restartSessions();
    }

    private function getKeyForToken(string $sessionName): string
    {
        return sprintf('behat_previous_session_token_%s', $sessionName);
    }
}
