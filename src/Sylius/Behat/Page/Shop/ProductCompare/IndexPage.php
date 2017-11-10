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
        $this->getElement('attributes')->find('table', 'table');
    }

    public function getErrorMessage()
    {
        $this->getDocument()->find('css', 'alert');
    }

    protected function getDefinedElements()
    {
        return array_merge(parent::getDefinedElements(), [
            'attributes' => '#sylius-product-compared-attributes'
        ]);
    }
}
