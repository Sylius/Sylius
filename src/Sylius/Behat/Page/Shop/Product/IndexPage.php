<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Page\Shop\Product;

use Sylius\Behat\Page\SymfonyPage;

/**
 * @author Anna Walasek <anna.walasek@lakion.com>
 */
class IndexPage extends SymfonyPage implements IndexPageInterface
{
    /**
     * {@inheritdoc}
     */
    public function isResourceOnPage($resourceName)
    {
        $elements= $this->getDocument()->findAll('css', 'div.header');
        foreach ($elements as $element) {
            if ($resourceName === $element->getText()) {
                return true;
            }
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function isEmpty()
    {
        $isEmpty = strpos($this->getDocument()->find('css', '.message')->getText(), 'There are no products to display');
        if (false === $isEmpty ) {
            return false;
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function getRouteName()
    {
        return 'sylius_shop_partial_product_index';
    }
}
