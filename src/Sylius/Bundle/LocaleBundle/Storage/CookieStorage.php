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
use Symfony\Component\HttpFoundation\Request;

/**
 * @author Joseph Bielawski <stloyd@gmail.com>
 */
class CookieStorage implements LocaleStorageInterface
{
    // Key used to store the locale in cookie.
    const COOKIE_KEY = '_sylius.locale';

    /**
     * @var Request
     */
    protected $request;

    /**
     * @param Request $request
     */
    public function setRequest($request)
    {
        $this->request = $request;
    }

    /**
     * {@inheritdoc}
     */
    public function getCurrentLocale($defaultLocale)
    {
        return $this->request->cookies->get(self::COOKIE_KEY, $defaultLocale);
    }

    /**
     * {@inheritdoc}
     */
    public function setCurrentLocale($locale)
    {
        return $this->request->cookies->set(self::COOKIE_KEY, $locale);
    }
}
