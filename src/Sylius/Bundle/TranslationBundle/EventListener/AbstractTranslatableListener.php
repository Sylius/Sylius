<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\TranslationBundle\EventListener;

use Sylius\Component\Translation\Provider\LocaleProviderInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
abstract class AbstractTranslatableListener
{
    /**
     * @var LocaleProviderInterface
     */
    protected $localeProvider;

    /**
     * Mapping.
     *
     * @var array
     */
    protected $configs;

    public function __construct(LocaleProviderInterface $localeProvider, array $configs)
    {
        $this->localeProvider = $localeProvider;
        $this->configs = $configs;
    }
}
