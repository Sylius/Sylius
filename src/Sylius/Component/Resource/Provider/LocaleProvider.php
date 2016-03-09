<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Resource\Provider;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class LocaleProvider implements LocaleProviderInterface
{
    /**
     * @var string
     */
    private $currentLocale;

    /**
     * @var string
     */
    private $fallbackLocale;

    /**
     * @param string $currentLocale
     * @param string $fallbackLocale
     */
    public function __construct($currentLocale, $fallbackLocale)
    {
        $this->currentLocale = $currentLocale;
        $this->fallbackLocale = $fallbackLocale;
    }

    /**
     * {@inheritdoc}
     */
    public function getCurrentLocale()
    {
        return $this->currentLocale;
    }

    /**
     * {@inheritdoc}
     */
    public function getFallbackLocale()
    {
        return $this->fallbackLocale;
    }
}
