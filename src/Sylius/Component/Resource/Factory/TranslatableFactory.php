<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Resource\Factory;

use Sylius\Component\Locale\Provider\LocaleProviderInterface;
use Sylius\Component\Resource\Exception\UnexpectedTypeException;
use Sylius\Component\Resource\Model\TranslatableInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class TranslatableFactory implements TranslatableFactoryInterface
{
    /**
     * @var FactoryInterface
     */
    private $factory;

    /**
     * @var LocaleProviderInterface
     */
    private $localeProvider;

    /**
     * @param FactoryInterface $factory
     * @param LocaleProviderInterface $localeProvider
     */
    public function __construct(FactoryInterface $factory, LocaleProviderInterface $localeProvider)
    {
        $this->factory = $factory;
        $this->localeProvider = $localeProvider;
    }

    /**
     * {@inheritdoc}
     */
    public function createNew()
    {
        $resource = $this->factory->createNew();

        if (!$resource instanceof TranslatableInterface) {
            throw new UnexpectedTypeException($resource, TranslatableInterface::class);
        }

        $resource->setCurrentLocale($this->localeProvider->getDefaultLocaleCode());
        $resource->setFallbackLocale($this->localeProvider->getFallbackLocaleCode());

        return $resource;
    }
}
