<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Locale\Context;

use Sylius\Component\Locale\Storage\LocaleStorageInterface;

/**
 * Default locale context implementation.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 * @author Joseph Bielawski <stloyd@gmail.com>
 */
class LocaleContext implements LocaleContextInterface
{
    /**
     * @var string
     */
    protected $defaultLocale;

    /**
     * Locale storage.
     *
     * @var LocaleStorageInterface
     */
    protected $storage;

    public function __construct(LocaleStorageInterface $storage, $defaultLocale)
    {
        $this->storage = $storage;
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
        return $this->storage->getCurrentLocale($this->defaultLocale);
    }

    /**
     * {@inheritdoc}
     */
    public function setLocale($locale)
    {
        return $this->storage->setCurrentLocale($locale);
    }
}
