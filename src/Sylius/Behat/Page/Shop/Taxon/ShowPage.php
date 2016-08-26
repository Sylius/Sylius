<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Page\Shop\Taxon;

use Sylius\Behat\Page\SymfonyPage;

/**
 * @author Anna Walasek <anna.walasek@lakion.com>
 */
class ShowPage extends SymfonyPage implements ShowPageInterface
{
    /**
     * {@inheritdoc}
     */
    public function getRouteName()
    {
        return 'sylius_shop_taxon_show';
    }

    /**
     * {@inheritdoc}
     */
    public function isProductOnList($productName)
    {
        return null !== $this->getDocument()->find('css', sprintf('.sylius-product-name:contains("%s")', $productName));
    }

    /**
     * {@inheritdoc}
     */
    public function isEmpty()
    {
        return false !== strpos($this->getDocument()->find('css', '.message')->getText(), 'There are no results to display');
    }

    /**
     * {@inheritdoc}
     */
    public function isProductWithPriceOnList($productName, $productPrice)
    {
        $container = $this->getDocument()->find('css', sprintf('.sylius-product-name:contains("%s")', $productName))->getParent();

        return $productPrice === $container->find('css', '.sylius-product-price')->getText();
    }
}
