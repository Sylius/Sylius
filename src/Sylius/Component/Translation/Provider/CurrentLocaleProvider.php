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
class CurrentLocaleProvider implements CurrentLocaleProviderInterface
{
    /**
     * @var string
     */
    private $currentLocale;

    /**
     * @param string $currentLocale
     */
    function __construct($currentLocale)
    {
        $this->currentLocale = $currentLocale;
    }

    /**
     * {@inheritdoc}
     */
    public function getLocale()
    {
        return $this->currentLocale;
    }
}
