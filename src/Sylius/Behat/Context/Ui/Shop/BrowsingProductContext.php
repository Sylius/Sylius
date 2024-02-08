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

namespace Sylius\Behat\Context\Ui\Shop;

use Behat\Behat\Context\Context;
use Sylius\Behat\Page\Shop\Product\ShowPageInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Webmozart\Assert\Assert;

final class BrowsingProductContext implements Context
{
    public function __construct(private ShowPageInterface $showPage)
    {
    }

    /**
     * @Then /^I should see (this product) in the ("([^"]*)" channel) in the shop$/
     */
    public function iShouldSeeThisProductInTheChannelInShop(ProductInterface $product, ChannelInterface $channel): void
    {
        Assert::true(null !== strpos($this->showPage->getCurrentUrl(), $channel->getHostname()));
        Assert::same($this->showPage->getName(), $product->getName());
    }
}
