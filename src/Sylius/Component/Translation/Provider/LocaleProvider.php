<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Translation\Provider;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class LocaleProvider implements LocaleProviderInterface
{
    /**
     * @var string
     */
    private $locale;

    /**
     * @param string $currentLocale
     */
    function __construct($locale)
    {
        $this->locale = $locale;
    }

    /**
     * {@inheritdoc}
     */
    public function getCurrentLocale()
    {
        return $this->locale;
    }

    public function getFallbackLocale()
    {
        return $this->locale;
    }
}
