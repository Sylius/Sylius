<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Service\Accessor;

use Behat\Mink\Session;
use Symfony\Component\HttpKernel\Client;
use Symfony\Component\HttpKernel\Profiler\Profiler;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
class EmailChecker implements EmailCheckerInterface
{
    /**
     * @var Profiler
     */
    private $symfonyProfiler;

    /**
     * @var Session
     */
    private $session;

    /**
     * @param Profiler $symfonyProfiler
     * @param Session $session
     */
    public function __construct(Profiler $symfonyProfiler, Session $session)
    {
        $this->symfonyProfiler = $symfonyProfiler;
        $this->session = $session;
    }

    /**
     * {@inheritdoc}
     */
    public function hasRecipient($recipient)
    {
        $messages = $this->getMessages();
        foreach ($messages as $message) {
            if (array_key_exists($recipient, $message->getTo())) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return \Swift_Mime_Message[]
     */
    private function getMessages()
    {
        /** @var Client $client */
        $client = $this->getClient();
        $client->followRedirects(false);
        $messages = [];

        do {
            $client->back();
            $response = $client->getResponse();
            $profile = $this->symfonyProfiler->loadProfileFromResponse($response);
            if (false === $profile) {
                continue;
            }
            $swiftMailerCollector = $profile->getCollector('swiftmailer');
            $messages = array_merge($messages, $swiftMailerCollector->getMessages());
        } while (empty($messages));
        $client->followRedirects();

        return $messages;
    }

    /**
     * @return Client
     */
    private function getClient()
    {
        return $this->session->getDriver()->getClient();
    }
}
