<?php

declare(strict_types=1);

namespace Sylius\Behat\Page\Shop\ProductCompare;

use Sylius\Behat\Page\SymfonyPage;

class IndexPage extends SymfonyPage implements IndexPageInterface
{
    /**
     * {@inheritdoc}
     */
    public function getRouteName()
    {
        return 'sylius_shop_product_compare_index';
    }

    public function getComparedAttributes()
    {
        return 2; // TODO
    }

    protected function getDefinedElements()
    {
        return array_merge(parent::getDefinedElements(), [
            'attributes' => '#sylius-product-attributes'
        ]);
    }
}
