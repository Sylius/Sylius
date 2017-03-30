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

use Sylius\Component\Attribute\Model\AttributeValueInterface;
use Sylius\Component\Product\Model\ProductAttributeInterface;
use Sylius\Component\Product\Model\ProductAttributeValueInterface;
use Sylius\Component\Product\Model\ProductInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Translation\Provider\TranslationLocaleProviderInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Webmozart\Assert\Assert;

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
            FormEvents::POST_SUBMIT => 'postSubmit',
        ];
    }

    /**
     * @param FormEvent $event
     *
     * @throws \InvalidArgumentException
     */
    public function preSetData(FormEvent $event)
    {
        /** @var ProductInterface $product */
        $product = $event->getData();

        Assert::isInstanceOf($product, ProductInterface::class);

        $defaultLocaleCode = $this->localeProvider->getDefaultLocaleCode();

        $attributes = $product->getAttributes()->filter(
            function (ProductAttributeValueInterface $attribute) use ($defaultLocaleCode) {
                return $attribute->getLocaleCode() === $defaultLocaleCode;
            }
        );

        foreach ($attributes as $attribute) {
            $this->resolveLocalizedAttributes($product, $attribute);
        }
    }

    /**
     * @param FormEvent $event
     *
     * @throws \InvalidArgumentException
     */
    public function postSubmit(FormEvent $event)
    {
        /** @var ProductInterface $product */
        $product = $event->getData();

        Assert::isInstanceOf($product, ProductInterface::class);

        /** @var AttributeValueInterface $attribute */
        foreach ($product->getAttributes() as $attribute) {
            if (null === $attribute->getValue()) {
                $product->removeAttribute($attribute);
            }
        }
    }

    /**
     * @param ProductInterface $product
     * @param ProductAttributeValueInterface $attribute
     */
    private function resolveLocalizedAttributes(ProductInterface $product, ProductAttributeValueInterface $attribute)
    {
        $localeCodes = $this->localeProvider->getDefinedLocalesCodes();

        foreach ($localeCodes as $localeCode) {
            if (!$product->hasAttributeByCodeAndLocale($attribute->getCode(), $localeCode)) {
                $attributeValue = $this->createProductAttributeValue($attribute->getAttribute(), $localeCode);
                $product->addAttribute($attributeValue);
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
