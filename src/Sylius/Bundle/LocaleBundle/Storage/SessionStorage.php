<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\LocaleBundle\Storage;

use Sylius\Component\Locale\Storage\LocaleStorageInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 * @author Joseph Bielawski <stloyd@gmail.com>
 */
class SessionStorage implements LocaleStorageInterface
{
    // Key used to store the locale in session.
    const SESSION_KEY = '_sylius.locale';

    /**
     * @var SessionInterface
     */
    protected $session;

    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }

    /**
     * {@inheritdoc}
     */
    public function getCurrentLocale($defaultLocale)
    {
        return $this->session->get(self::SESSION_KEY, $defaultLocale);
    }

    /**
     * {@inheritdoc}
     */
    public function setCurrentLocale($locale)
    {
        return $this->session->set(self::SESSION_KEY, $locale);
    }
}
