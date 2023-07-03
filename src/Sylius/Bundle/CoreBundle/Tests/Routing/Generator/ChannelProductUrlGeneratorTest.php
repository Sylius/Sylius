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

namespace Routing\Generator;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\CoreBundle\Routing\Generator\ChannelProductUrlGenerator;
use Sylius\Bundle\CoreBundle\Routing\Generator\ChannelProductUrlGeneratorInterface;
use Sylius\Component\Core\Model\Channel;
use Sylius\Component\Core\Model\Product;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductTranslation;
use Sylius\Component\Core\Model\ProductTranslationInterface;
use Sylius\Component\Locale\Context\LocaleContextInterface;
use Sylius\Component\Locale\Model\Locale;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final class ChannelProductUrlGeneratorTest extends TestCase
{
    private LocaleContextInterface|MockObject $localeContext;

    private UrlGeneratorInterface|MockObject $urlGenerator;

    protected function setUp(): void
    {
        $this->localeContext = $this->createMock(LocaleContextInterface::class);
        $this->urlGenerator = $this->createMock(UrlGeneratorInterface::class);
    }

    /** @test */
    public function it_generates_url_using_administrator_locale_translations(): void
    {
        $product = new Product();
        $product->addTranslation($this->createProductTranslation($product, 'pl_PL', 'polish-product-slug'));
        $product->addTranslation($this->createProductTranslation($product, 'en_US', 'english-product-slug'));

        $channelLocale = new Locale();
        $channelLocale->setCode('en_US');

        $channel = new Channel();
        $channel->setDefaultLocale($channelLocale);

        $this->localeContext->expects($this->once())->method('getLocaleCode')->willReturn('pl_PL');
        $this->urlGenerator
            ->expects($this->once())
            ->method('generate')
            ->with('sylius_shop_product_show', [
                'slug' => 'polish-product-slug',
                '_locale' => 'pl_PL',
            ])
            ->willReturn('/pl_PL/products/polish-product-slug')
        ;

        $generator = $this->createGenerator();
        $generatedUrl = $generator->generate($product, $channel);

        $this->assertSame('/pl_PL/products/polish-product-slug', $generatedUrl);
    }

    /** @test */
    public function it_generates_url_using_channel_default_locale_translations(): void
    {
        $product = new Product();
        $product->addTranslation($this->createProductTranslation($product, 'pl_PL', ''));
        $product->addTranslation($this->createProductTranslation($product, 'en_US', 'english-product-slug'));

        $channelLocale = new Locale();
        $channelLocale->setCode('en_US');

        $channel = new Channel();
        $channel->setDefaultLocale($channelLocale);

        $this->localeContext->expects($this->once())->method('getLocaleCode')->willReturn('de_DE');
        $this->urlGenerator
            ->expects($this->once())
            ->method('generate')
            ->with('sylius_shop_product_show', [
                'slug' => 'english-product-slug',
                '_locale' => 'en_US',
            ])
            ->willReturn('/en_US/products/english-product-slug')
        ;

        $generator = $this->createGenerator();
        $generatedUrl = $generator->generate($product, $channel);

        $this->assertSame('/en_US/products/english-product-slug', $generatedUrl);
    }

    /** @test */
    public function it_generates_url_using_any_translation_enabled_in_channel_if_administrator_and_channel_default_translations_does_not_exist(): void
    {
        $product = new Product();
        $product->addTranslation($this->createProductTranslation($product, 'pl_PL', ''));
        $product->addTranslation($this->createProductTranslation($product, 'en_US', ''));
        $product->addTranslation($this->createProductTranslation($product, 'de_DE', 'german-product-slug'));
        $product->addTranslation($this->createProductTranslation($product, 'fr_FR', 'french-product-slug'));

        $enUsLocale = new Locale();
        $enUsLocale->setCode('en_US');

        $deDeLocale = new Locale();
        $deDeLocale->setCode('de_DE');

        $channel = new Channel();
        $channel->setDefaultLocale($enUsLocale);
        $channel->addLocale($enUsLocale);
        $channel->addLocale($deDeLocale);

        $this->localeContext->expects($this->once())->method('getLocaleCode')->willReturn('es_ES');
        $this->urlGenerator
            ->expects($this->once())
            ->method('generate')
            ->with('sylius_shop_product_show', [
                'slug' => 'german-product-slug',
                '_locale' => 'de_DE',
            ])
            ->willReturn('/de_DE/products/german-product-slug')
        ;

        $generator = $this->createGenerator();
        $generatedUrl = $generator->generate($product, $channel);

        $this->assertSame('/de_DE/products/german-product-slug', $generatedUrl);
    }

    /** @test */
    public function it_generates_null_when_no_translations_available(): void
    {
        $product = new Product();

        $channelLocale = new Locale();
        $channelLocale->setCode('en_US');

        $channel = new Channel();
        $channel->setDefaultLocale($channelLocale);

        $this->localeContext->expects($this->once())->method('getLocaleCode')->willReturn('fr_FR');
        $this->urlGenerator
            ->expects($this->never())
            ->method('generate')
        ;

        $generator = $this->createGenerator();
        $generatedUrl = $generator->generate($product, $channel);

        $this->assertNull($generatedUrl);
    }

    private function createGenerator(): ChannelProductUrlGeneratorInterface
    {
        return new ChannelProductUrlGenerator($this->localeContext, $this->urlGenerator, unsecuredUrls: false);
    }

    private function createProductTranslation(ProductInterface $product, string $localeCode, string $slug = ''): ProductTranslationInterface
    {
        $productTranslation = new ProductTranslation();
        $productTranslation->setTranslatable($product);
        $productTranslation->setLocale($localeCode);
        $productTranslation->setSlug($slug);

        return $productTranslation;
    }
}
