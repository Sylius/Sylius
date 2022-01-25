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

namespace Sylius\Behat\Context\Ui\Admin;

use Behat\Behat\Context\Context;
use Sylius\Behat\Page\Admin\Crud\CreatePageInterface;
use Sylius\Component\Core\Formatter\StringInflector;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\TaxonInterface;

final class ProductCreationContext implements Context
{
    public function __construct(private CreatePageInterface $createPage)
    {
    }

    /**
     * @When /^I create a new simple product ("[^"]+") priced at "(?:€|£|\$)([^"]+)" with ("[^"]+" taxon) in the ("[^"]+" channel)$/
     */
    public function iCreateANewVariantPricedAtForProductInTheChannel(
        string $name,
        string $price,
        TaxonInterface $taxon,
        ChannelInterface $channel
    ): void {
        $locale = $channel->getDefaultLocale()->getCode();

        $this->createPage->open();

        $this->createPage->nameItIn($name, $locale);
        $this->createPage->specifySlugIn(StringInflector::nameToSlug($name), $locale);
        $this->createPage->specifyCode(str_replace('"', '', StringInflector::nameToUppercaseCode($name)));
        $this->createPage->specifyPrice($channel, $price);
        $this->createPage->selectMainTaxon($taxon);

        $this->createPage->create();
    }
}
