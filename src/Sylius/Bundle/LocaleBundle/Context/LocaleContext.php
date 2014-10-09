<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\LocaleBundle\Context;

use Sylius\Component\Locale\Context\LocaleContextInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

/**
 * Default locale context implementation, which uses session to store the user selected locale.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class LocaleContext implements LocaleContextInterface
{
    // Key used to store the locale in session.
    const SESSION_KEY = '_sylius.locale';

    /**
     * @var SessionInterface
     */
    protected $session;

    /**
     * @var string
     */
    protected $defaultLocale;

    public function __construct(SessionInterface $session, $defaultLocale)
    {
        $this->session = $session;
        $this->defaultLocale = $defaultLocale;
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultLocale()
    {
        return $this->defaultLocale;
    }

    /**
     * {@inheritdoc}
     */
    public function getLocale()
    {
        if (!$this->session->isStarted()) {
            return $this->defaultLocale;
        }

        return $this->session->get(self::SESSION_KEY, $this->defaultLocale);
    }

    /**
     * {@inheritdoc}
     */
    public function setLocale($locale)
    {
        return $this->session->set(self::SESSION_KEY, $locale);
    }
}
