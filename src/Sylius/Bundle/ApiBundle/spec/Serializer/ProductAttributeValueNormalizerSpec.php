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

namespace spec\Sylius\Bundle\ApiBundle\Serializer;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Locale\Provider\LocaleProviderInterface;
use Sylius\Component\Product\Model\ProductAttributeInterface;
use Sylius\Component\Product\Model\ProductAttributeValueInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

final class ProductAttributeValueNormalizerSpec extends ObjectBehavior
{
    function let(LocaleProviderInterface $localeProvider): void
    {
        $this->beConstructedWith($localeProvider, 'en_US');
    }

    function it_supports_only_product_attribute_value_interface(
        ProductAttributeValueInterface $productAttributeValue,
        OrderInterface $order,
    ): void {
        $this->supportsNormalization($productAttributeValue)->shouldReturn(true);
        $this->supportsNormalization($order)->shouldReturn(false);
    }

    function it_supports_the_normalizer_has_not_called_yet(ProductAttributeValueInterface $productAttributeValue): void
    {
        $this
            ->supportsNormalization($productAttributeValue, null, [])
            ->shouldReturn(true)
        ;

        $this
            ->supportsNormalization($productAttributeValue, null, ['sylius_product_attribute_value_normalizer_already_called' => true])
            ->shouldReturn(false)
        ;
    }

    function it_serializes_product_attribute_select_values(
        NormalizerInterface $normalizer,
        ProductAttributeValueInterface $productAttributeValue,
        ProductAttributeInterface $productAttribute,
        LocaleProviderInterface $localeProvider,
    ): void {
        $normalizer
            ->normalize($productAttributeValue, null, ['sylius_product_attribute_value_normalizer_already_called' => true])
            ->willReturn([])
        ;

        $productAttributeValue->getType()->willReturn('select');
        $productAttributeValue->getAttribute()->willReturn($productAttribute);

        $productAttribute->getConfiguration()->willReturn([
            'choices' => [
                'uuid1' => [
                    'de_DE' => 'de text1',
                    'pl_PL' => 'pl text1',
                    'fr_FR' => 'fr text1',
                    'en_US' => 'en text1',
                    'zu_ZA' => 'zu text1',
                ],
                'uuid2' => [
                    'de_DE' => 'de text2',
                    'fr_FR' => 'fr text2',
                    'en_US' => 'en text2',
                    'zu_ZA' => 'zu text2',
                ],
                'uuid3' => [
                    'de_DE' => 'de text3',
                    'en_US' => 'en text3',
                    'zu_ZA' => 'zu text3',
                ],
                'uuid4' => [
                    'de_DE' => 'de text4',
                    'zu_ZA' => 'zu text4',
                ],
            ],
        ]);

        $productAttributeValue->getValue()->willReturn(['uuid1', 'uuid2', 'uuid3', 'uuid4']);

        $productAttributeValue->getLocaleCode()->willReturn('pl_PL');
        $localeProvider->getDefaultLocaleCode()->willReturn('fr_FR');

        $this->setNormalizer($normalizer);
        $this->normalize($productAttributeValue, null, [])->shouldReturn([
            'value' => [
                'pl text1',
                'fr text2',
                'en text3',
                'de text4',
            ],
        ]);
    }

    function it_serializes_product_attribute_select_values_when_attribute_has_no_value(
        NormalizerInterface $normalizer,
        ProductAttributeValueInterface $productAttributeValue,
        ProductAttributeInterface $productAttribute,
        LocaleProviderInterface $localeProvider,
    ): void {
        $normalizer
            ->normalize($productAttributeValue, null, ['sylius_product_attribute_value_normalizer_already_called' => true])
            ->willReturn([])
        ;

        $productAttributeValue->getType()->willReturn('select');
        $productAttributeValue->getAttribute()->willReturn($productAttribute);

        $productAttributeValue->getValue()->willReturn(null);

        $productAttribute->getConfiguration()->shouldNotBeCalled();
        $productAttributeValue->getLocaleCode()->shouldNotBeCalled();
        $localeProvider->getDefaultLocaleCode()->shouldNotBeCalled();

        $this->setNormalizer($normalizer);
        $this->normalize($productAttributeValue, null, [])->shouldReturn(['value' => []]);
    }

    function it_serializes_product_attribute_date_values(
        NormalizerInterface $normalizer,
        ProductAttributeValueInterface $productAttributeValue,
        ProductAttributeInterface $productAttribute,
        LocaleProviderInterface $localeProvider,
    ): void {
        $normalizer
            ->normalize($productAttributeValue, null, ['sylius_product_attribute_value_normalizer_already_called' => true])
            ->willReturn([])
        ;

        $productAttributeValue->getType()->willReturn('date');
        $productAttributeValue->getAttribute()->willReturn($productAttribute);

        $productAttributeValue->getValue()->willReturn(new \DateTime('2022-01-01 14:16:53'));

        $productAttributeValue->getLocaleCode()->willReturn('pl_PL');
        $localeProvider->getDefaultLocaleCode()->willReturn('fr_FR');

        $this->setNormalizer($normalizer);
        $this->normalize($productAttributeValue, null, [])->shouldReturn([
            'value' => '2022-01-01',
        ]);
    }

    function it_does_not_change_the_value_on_integer_type(
        NormalizerInterface $normalizer,
        ProductAttributeValueInterface $productAttributeValue,
        ProductAttributeInterface $productAttribute,
        LocaleProviderInterface $localeProvider,
    ): void {
        $normalizer
            ->normalize($productAttributeValue, null, ['sylius_product_attribute_value_normalizer_already_called' => true])
            ->willReturn(42)
        ;

        $productAttributeValue->getType()->willReturn('integer');

        $this->setNormalizer($normalizer);
        $this->normalize($productAttributeValue, null, [])->shouldReturn(42);
    }
}
