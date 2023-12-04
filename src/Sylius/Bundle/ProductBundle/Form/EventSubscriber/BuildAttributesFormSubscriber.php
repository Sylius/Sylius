<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

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

final class BuildAttributesFormSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private FactoryInterface $attributeValueFactory,
        private TranslationLocaleProviderInterface $localeProvider,
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            FormEvents::PRE_SET_DATA => 'preSetData',
            FormEvents::POST_SUBMIT => 'postSubmit',
        ];
    }

    /**
     * @throws \InvalidArgumentException
     */
    public function preSetData(FormEvent $event): void
    {
        $product = $event->getData();

        /** @var ProductInterface $product */
        Assert::isInstanceOf($product, ProductInterface::class);

        $defaultLocaleCode = $this->localeProvider->getDefaultLocaleCode();

        $attributes = $product->getAttributes()->filter(
            fn (AttributeValueInterface $attribute) => $attribute->getLocaleCode() === $defaultLocaleCode,
        );

        /** @var ProductAttributeValueInterface $attribute */
        foreach ($attributes as $attribute) {
            $this->resolveLocalizedAttributes($product, $attribute);
        }
    }

    /**
     * @throws \InvalidArgumentException
     */
    public function postSubmit(FormEvent $event): void
    {
        $product = $event->getData();

        /** @var ProductInterface $product */
        Assert::isInstanceOf($product, ProductInterface::class);

        /** @var AttributeValueInterface $attribute */
        foreach ($product->getAttributes() as $attribute) {
            if (null === $attribute->getValue()) {
                $product->removeAttribute($attribute);
            }
        }
    }

    private function resolveLocalizedAttributes(ProductInterface $product, ProductAttributeValueInterface $productAttributeValue): void
    {
        $localeCodes = $this->localeProvider->getDefinedLocalesCodes();

        foreach ($localeCodes as $localeCode) {
            if (!$product->hasAttributeByCodeAndLocale($productAttributeValue->getCode(), $localeCode)) {
                $attribute = $productAttributeValue->getAttribute();
                Assert::isInstanceOf($attribute, ProductAttributeInterface::class);
                $attributeValue = $this->createProductAttributeValue($attribute, $localeCode);
                $product->addAttribute($attributeValue);
            }
        }
    }

    private function createProductAttributeValue(
        ProductAttributeInterface $attribute,
        string $localeCode,
    ): ProductAttributeValueInterface {
        /** @var ProductAttributeValueInterface $attributeValue */
        $attributeValue = $this->attributeValueFactory->createNew();
        $attributeValue->setAttribute($attribute);
        $attributeValue->setLocaleCode($localeCode);

        return $attributeValue;
    }
}
