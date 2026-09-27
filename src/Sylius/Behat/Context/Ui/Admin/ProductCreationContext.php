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

namespace Sylius\Behat\Context\Ui\Admin;

use Behat\Behat\Context\Context;
use Behat\Step\When;
use Sylius\Behat\Element\Admin\Product\ChannelPricingsFormElementInterface;
use Sylius\Behat\Element\Admin\Product\TaxonomyFormElementInterface;
use Sylius\Behat\Element\Admin\Product\TranslationsFormElementInterface;
use Sylius\Behat\Page\Admin\Product\CreateSimpleProductPageInterface;
use Sylius\Component\Core\Formatter\StringInflector;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\TaxonInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

final readonly class ProductCreationContext implements Context
{
    public function __construct(
        private CreateSimpleProductPageInterface $createPage,
        private TranslationsFormElementInterface $productTranslationsFormElement,
        private ChannelPricingsFormElementInterface $productChannelPricingsFormElement,
        private TaxonomyFormElementInterface $productTaxonomyFormElement,
        private SluggerInterface $slugger,
    ) {
    }

    #[When('/^I create a new simple product ("[^"]+") priced at "(?:€|£|\$)([^"]+)" with ("[^"]+" taxon) in the ("[^"]+" channel)$/')]
    public function iCreateANewSimpleProductPricedAtWithTaxonInTheChannel(
        string $name,
        string $price,
        TaxonInterface $taxon,
        ChannelInterface $channel,
    ): void {
        $localeCode = $channel->getDefaultLocale()->getCode();
        $slug = $this->slugger->slug($name)->lower()->toString();

        $this->createPage->open();

        $this->productTranslationsFormElement->nameItIn(str_replace('"', '', $name), $localeCode);
        $this->productTranslationsFormElement->specifySlugIn($slug, $localeCode);
        $this->createPage->specifyCode(str_replace('"', '', StringInflector::nameToUppercaseCode($name)));

        $this->productChannelPricingsFormElement->specifyPrice($channel, $price);
        $this->createPage->checkChannel($channel->getCode());

        $this->productTaxonomyFormElement->selectMainTaxon($taxon->getName());
        $this->productTaxonomyFormElement->checkProductTaxon($taxon);

        $this->createPage->create();
    }
}
