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

use Sylius\Component\Resource\Exception\UnexpectedTypeException;
use Sylius\Component\Resource\Model\TranslatableInterface;
use Sylius\Component\Resource\Translation\Provider\TranslationLocaleProviderInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
final class TranslatableFactory implements TranslatableFactoryInterface
{
    /**
     * @var FactoryInterface
     */
    private $factory;

    /**
     * @var TranslationLocaleProviderInterface
     */
    private $localeProvider;

    /**
     * @param FactoryInterface $factory
     * @param TranslationLocaleProviderInterface $localeProvider
     */
    public function __construct(FactoryInterface $factory, TranslationLocaleProviderInterface $localeProvider)
    {
        $this->factory = $factory;
        $this->localeProvider = $localeProvider;
    }

    /**
     * {@inheritdoc}
     *
     * @throws UnexpectedTypeException
     */
    public function createNew()
    {
        $resource = $this->factory->createNew();

        if (!$resource instanceof TranslatableInterface) {
            throw new UnexpectedTypeException($resource, TranslatableInterface::class);
        }

        $resource->setCurrentLocale($this->localeProvider->getDefaultLocaleCode());
        $resource->setFallbackLocale($this->localeProvider->getDefaultLocaleCode());

        return $resource;
    }
}
