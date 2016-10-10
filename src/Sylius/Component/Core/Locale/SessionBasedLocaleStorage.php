<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Core\Locale;

use Sylius\Component\Channel\Model\ChannelInterface;
use Sylius\Component\Locale\Context\LocaleNotFoundException;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class SessionBasedLocaleStorage implements LocaleStorageInterface
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
    public function set(ChannelInterface $channel, $localeCode)
    {
        $this->session->set($this->provideKey($channel), $localeCode);
    }

    /**
     * {@inheritdoc}
     */
    public function get(ChannelInterface $channel)
    {
        $localeCode = $this->session->get($this->provideKey($channel));
        if (null === $localeCode) {
            throw new LocaleNotFoundException('No locale is set for current channel!');
        }

        return $localeCode;
    }

    /**
     * {@inheritdoc}
     */
    private function provideKey(ChannelInterface $channel)
    {
        return '_locale_' . $channel->getCode();
    }
}
