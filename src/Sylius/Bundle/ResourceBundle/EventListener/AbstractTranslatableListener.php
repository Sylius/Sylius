<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ResourceBundle\EventListener;

use Sylius\Component\Resource\Metadata\RegistryInterface;
use Sylius\Component\Resource\Provider\LocaleProviderInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
abstract class AbstractTranslatableListener
{
    /**
     * @var RegistryInterface
     */
    protected $registry;

    /**
     * @var LocaleProviderInterface
     */
    protected $localeProvider;

    /**
     * @param RegistryInterface $registry
     * @param LocaleProviderInterface $localeProvider
     */
    public function __construct(RegistryInterface $registry, LocaleProviderInterface $localeProvider)
    {
        $this->registry = $registry;
        $this->localeProvider = $localeProvider;
    }
}
