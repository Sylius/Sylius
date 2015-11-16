<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\TranslationBundle\Doctrine;

use Sylius\Component\Resource\Metadata\ResourceRegistryInterface;
use Sylius\Component\Translation\Provider\LocaleProviderInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
abstract class AbstractTranslatableListener
{
    /**
     * @var ResourceRegistryInterface
     */
    protected $registry;

    /**
     * @var LocaleProviderInterface
     */
    protected $localeProvider;

    /**
     * @param ResourceRegistryInterface $registry
     * @param LocaleProviderInterface $localeProvider
     */
    public function __construct(ResourceRegistryInterface $registry, LocaleProviderInterface $localeProvider)
    {
        $this->registry = $registry;
        $this->localeProvider = $localeProvider;
    }


}
