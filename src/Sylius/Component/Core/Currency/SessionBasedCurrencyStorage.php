<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Core\Currency;

use Sylius\Component\Channel\Model\ChannelInterface;
use Sylius\Component\Currency\Context\CurrencyNotFoundException;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class SessionBasedCurrencyStorage implements CurrencyStorageInterface
{
    /**
     * @var SessionInterface
     */
    private $session;

    /**
     * @param SessionInterface $session
     */
    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }

    /**
     * {@inheritdoc}
     */
    public function set(ChannelInterface $channel, $currencyCode)
    {
        $this->session->set($this->provideKey($channel), $currencyCode);
    }

    /**
     * {@inheritdoc}
     */
    public function get(ChannelInterface $channel)
    {
        $currencyCode = $this->session->get($this->provideKey($channel));
        if (null === $currencyCode) {
            throw new CurrencyNotFoundException('No currency is set for current channel!');
        }

        return $currencyCode;
    }

    /**
     * {@inheritdoc}
     */
    private function provideKey(ChannelInterface $channel)
    {
        return '_currency_' . $channel->getCode();
    }
}
