<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Translation\Factory;

use Sylius\Component\Resource\Exception\UnexpectedTypeException;
use Sylius\Component\Resource\Factory\ResourceFactory;
use Sylius\Component\Translation\Model\TranslatableInterface;
use Sylius\Component\Translation\Provider\LocaleProviderInterface;

/**
 * Translatable resource factory class.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class TranslatableResourceFactory extends ResourceFactory
{
    /**
     * @var LocaleProviderInterface
     */
    private $localeProvider;

    /**
     * @param string $class
     * @param LocaleProviderInterface $localeProvider
     */
    public function __construct($class, LocaleProviderInterface $localeProvider)
    {
        parent::__construct($class);

        $this->localeProvider = $localeProvider;
    }

    /**
     * {@inheritdoc}
     */
    public function createNew()
    {
        $resource = parent::createNew();

        if (!$resource instanceof TranslatableInterface) {
            throw new UnexpectedTypeException($resource, 'Sylius\\Component\\Translation\\Model\\TranslatableInterface');
        }

        $resource->setCurrentLocale($this->localeProvider->getCurrentLocale());
        $resource->setFallbackLocale($this->localeProvider->getFallbackLocale());

        return $resource;
    }
}
