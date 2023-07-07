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

namespace Sylius\Bundle\CoreBundle\Tests\Provider;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\CoreBundle\Provider\ChannelBasedProductTranslationProvider;
use Sylius\Bundle\CoreBundle\Provider\ChannelBasedProductTranslationProviderInterface;
use Sylius\Component\Core\Model\Channel;
use Sylius\Component\Core\Model\Product;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductTranslation;
use Sylius\Component\Core\Model\ProductTranslationInterface;
use Sylius\Component\Locale\Context\LocaleContextInterface;
use Sylius\Component\Locale\Model\Locale;

final class ChannelBasedProductTranslationProviderTest extends TestCase
{
    private LocaleContextInterface|MockObject $localeContext;

    protected function setUp(): void
    {
        $this->localeContext = $this->createMock(LocaleContextInterface::class);
    }

    /** @test */
    public function it_provides_product_translation_using_administrator_locale(): void
    {
        $product = new Product();
        $product->addTranslation($this->createProductTranslation($product, 'pl_PL', 'polish-product-slug'));
        $product->addTranslation($this->createProductTranslation($product, 'en_US', 'english-product-slug'));

        $channelLocale = new Locale();
        $channelLocale->setCode('en_US');

        $channel = new Channel();
        $channel->setDefaultLocale($channelLocale);

        $this->localeContext->expects($this->once())->method('getLocaleCode')->willReturn('pl_PL');

        $provider = $this->createProvider();
        $productTranslation = $provider->provide($product, $channel);

        $this->assertSame('polish-product-slug', $productTranslation->getSlug());
        $this->assertSame('pl_PL', $productTranslation->getLocale());
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

        $provider = $this->createProvider();
        $productTranslation = $provider->provide($product, $channel);

        $this->assertSame('english-product-slug', $productTranslation->getSlug());
        $this->assertSame('en_US', $productTranslation->getLocale());
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

        $provider = $this->createProvider();
        $productTranslation = $provider->provide($product, $channel);

        $this->assertSame('german-product-slug', $productTranslation->getSlug());
        $this->assertSame('de_DE', $productTranslation->getLocale());
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

        $provider = $this->createProvider();
        $productTranslation = $provider->provide($product, $channel);

        $this->assertNull($productTranslation);
    }

    private function createProvider(): ChannelBasedProductTranslationProviderInterface
    {
        return new ChannelBasedProductTranslationProvider($this->localeContext);
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
