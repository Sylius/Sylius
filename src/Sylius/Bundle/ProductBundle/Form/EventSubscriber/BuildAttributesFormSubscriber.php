<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ProductBundle\Form\EventSubscriber;

use Sylius\Component\Product\Model\ProductAttributeInterface;
use Sylius\Component\Product\Model\ProductAttributeValueInterface;
use Sylius\Component\Product\Model\ProductInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Translation\Provider\TranslationLocaleProviderInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

/**
 * @author Grzegorz Sadowski <grzegorz.sadowski@lakion.com>
 */
final class BuildAttributesFormSubscriber implements EventSubscriberInterface
{
    /**
     * @var FactoryInterface
     */
    private $attributeValueFactory;

    /**
     * @var TranslationLocaleProviderInterface
     */
    private $localeProvider;

    /**
     * @param FactoryInterface $attributeValueFactory
     * @param TranslationLocaleProviderInterface $localeProvider
     */
    public function __construct(
        FactoryInterface $attributeValueFactory,
        TranslationLocaleProviderInterface $localeProvider
    ) {
        $this->attributeValueFactory = $attributeValueFactory;
        $this->localeProvider = $localeProvider;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            FormEvents::PRE_SET_DATA => 'preSetData',
        ];
    }

    /**
     * @param FormEvent $event
     */
    public function preSetData(FormEvent $event)
    {
        /** @var ProductInterface $product */
        $product = $event->getData();

        $localeCodes = $this->localeProvider->getDefinedLocalesCodes();
        $defaultLocaleCode = $this->localeProvider->getDefaultLocaleCode();

        $attributes = $product->getAttributes()->filter(
            function (ProductAttributeValueInterface $attribute) use ($defaultLocaleCode) {
                return $attribute->getLocaleCode() === $defaultLocaleCode;
            }
        );

        foreach ($attributes as $attribute) {
            foreach ($localeCodes as $localeCode) {
                if (!$product->hasAttributeByCodeAndLocale($attribute->getCode(), $localeCode)) {
                    $attributeValue = $this->createProductAttributeValue($attribute->getAttribute(), $localeCode);
                    $product->addAttribute($attributeValue);
                }
            }
        }
    }

    /**
     * @param ProductAttributeInterface $attribute
     * @param string $localeCode
     *
     * @return ProductAttributeValueInterface
     */
    private function createProductAttributeValue(ProductAttributeInterface $attribute, $localeCode)
    {
        /** @var ProductAttributeValueInterface $attributeValue */
        $attributeValue = $this->attributeValueFactory->createNew();
        $attributeValue->setAttribute($attribute);
        $attributeValue->setLocaleCode($localeCode);

        return $attributeValue;
    }
}
