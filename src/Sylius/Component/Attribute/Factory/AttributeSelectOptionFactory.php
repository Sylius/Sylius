<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Component\Attribute\Factory;


use Sylius\Component\Attribute\Model\AttributeInterface;
use Sylius\Component\Attribute\Model\AttributeSelectOptionInterface;
use Sylius\Component\Resource\Exception\UnexpectedTypeException;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Translation\Provider\TranslationLocaleProviderInterface;

/**
 * @author Asier Marqués <asier@simettric.com>
 */
class AttributeSelectOptionFactory implements AttributeSelectOptionFactoryInterface
{

    /**
     * @var TranslationLocaleProviderInterface
     */
    private $localeProvider;

    private $class_name;

    /**
     * @param FactoryInterface $factory
     * @param TranslationLocaleProviderInterface $localeProvider
     */
    public function __construct($class_name, TranslationLocaleProviderInterface $localeProvider)
    {
        $this->localeProvider = $localeProvider;
        $this->class_name     = $class_name;
    }


    /**
     * @return AttributeSelectOptionInterface
     */
    public function createNew()
    {
        $resource = new $this->class_name;

        if (!$resource instanceof AttributeSelectOptionInterface) {
            throw new UnexpectedTypeException($resource, AttributeSelectOptionInterface::class);
        }

        $resource->setCurrentLocale($this->localeProvider->getDefaultLocaleCode());
        $resource->setFallbackLocale($this->localeProvider->getDefaultLocaleCode());

        return $resource;
    }



    public function createForAttribute(AttributeInterface $attribute): AttributeSelectOptionInterface
    {
        $selectOption = $this->createNew();
        $selectOption->setAttribute($attribute);
        return $selectOption;
    }
}
