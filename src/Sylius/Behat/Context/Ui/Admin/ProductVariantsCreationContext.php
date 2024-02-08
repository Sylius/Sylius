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
use Sylius\Behat\Page\Admin\Crud\CreatePageInterface;
use Sylius\Component\Core\Formatter\StringInflector;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ProductInterface;

final class ProductVariantsCreationContext implements Context
{
    public function __construct(private CreatePageInterface $createPage)
    {
    }

    /**
     * @When /^I create a new "([^"]+)" variant priced at "(?:€|£|\$)([^"]+)" for ("[^"]+" product) in the ("[^"]+" channel)$/
     */
    public function iCreateANewVariantPricedAtForProductInTheChannel(
        string $name,
        string $price,
        ProductInterface $product,
        ChannelInterface $channel,
    ): void {
        $this->createPage->open(['productId' => $product->getId()]);

        $this->createPage->specifyCode(str_replace('"', '', StringInflector::nameToUppercaseCode($name)));
        $this->createPage->nameItIn($name, $channel->getDefaultLocale()->getCode());
        $this->createPage->specifyPrice($price, $channel);

        $this->createPage->create();
    }
}
