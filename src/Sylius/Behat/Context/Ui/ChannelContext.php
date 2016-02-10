<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Context\Ui;

use Behat\Behat\Context\Context;
use Behat\Mink\Mink;
use Behat\Mink\Session;
use Behat\MinkExtension\Context\MinkAwareContext;
use Behat\Symfony2Extension\Driver\KernelDriver;
use Sylius\Component\Core\Model\ChannelInterface;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class ChannelContext implements Context
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
        if (!isset($minkParameters['base_url'])) {
            $minkParameters['base_url'] = null;
        }

        $this->minkSession = $minkSession;
        $this->minkParameters = $minkParameters;
    }

    /**
     * @When I change my current channel to :channel
     */
    public function iChangeMyCurrentChannelTo(ChannelInterface $channel)
    {
        $this->prepareSessionIfNeeded();

        $this->minkSession->setCookie('_channel_code', $channel->getCode());
    }

    private function prepareSessionIfNeeded()
    {
        if ($this->minkSession->getDriver() instanceof KernelDriver) {
            return;
        }

        if (false !== strpos($this->minkSession->getCurrentUrl(), $this->minkParameters['base_url'])) {
            return;
        }

        $this->minkSession->visit(rtrim($this->minkParameters['base_url'], '/') . '/');
    }
}
