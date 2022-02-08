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

final class SessionService implements SessionServiceInterface
{
    private Mink $mink;

    private SharedStorageInterface $sharedStorage;

    private SecurityServiceInterface $securityService;

    private const SESSION_CHROME_HEADLESS_SECOND = 'chrome_headless_second_session';

    public function __construct(Mink $mink, SharedStorageInterface $sharedStorage, SecurityServiceInterface $securityService)
    {
        $this->mink = $mink;
        $this->sharedStorage = $sharedStorage;
        $this->securityService = $securityService;
    }

    public function changeSession(): void
    {
        $sessionName = self::SESSION_CHROME_HEADLESS_SECOND;

        $this->savePreviousSession();

        $this->mink->setDefaultSessionName($sessionName);

        $this->mink->restartSessions();

        if ($this->sharedStorage->has(sprintf('behat_previous_session_token_%s', $sessionName))) {
            $this->securityService->restoreToken($this->sharedStorage->get(sprintf('behat_previous_session_token_%s', $sessionName)));
        }
    }

    public function restorePreviousSession(): void
    {
        if (!$this->sharedStorage->has('behat_previous_session_name')) {
            return;
        }

        /** @var string $sessionName */
        $sessionName = $this->sharedStorage->get('behat_previous_session_name');

        $this->savePreviousSession();

        $this->mink->setDefaultSessionName($sessionName);

        $this->mink->restartSessions();

        $this->securityService->restoreToken($this->sharedStorage->get(sprintf('behat_previous_session_token_%s', $sessionName)));
    }

    private function savePreviousSession(): void
    {
        /** @var string $sessionName */
        $sessionName = $this->mink->getDefaultSessionName();

        $this->sharedStorage->set('behat_previous_session_name', $sessionName);
        $this->sharedStorage->set(sprintf('behat_previous_session_token_%s', $sessionName), $this->securityService->getCurrentToken());
    }
}
