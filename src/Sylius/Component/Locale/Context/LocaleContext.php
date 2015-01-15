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

use Sylius\Component\Storage\StorageInterface;

/**
 * Default locale context implementation.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 * @author Joseph Bielawski <stloyd@gmail.com>
 */
class LocaleContext implements LocaleContextInterface
{
    /**
     * Default locale.
     *
     * @var string
     */
    protected $defaultLocale;

    /**
     * Locale storage.
     *
     * @var StorageInterface
     */
    protected $storage;

    public function __construct(StorageInterface $storage, $defaultLocale)
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
        return $this->storage->getData(self::STORAGE_KEY, $this->defaultLocale);
    }

    /**
     * {@inheritdoc}
     */
    public function setLocale($locale)
    {
        return $this->storage->setData(self::STORAGE_KEY, $locale);
    }
}
