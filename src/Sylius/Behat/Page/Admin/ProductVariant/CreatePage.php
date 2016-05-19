<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Page\Admin\ProductVariant;

use Sylius\Behat\Behaviour\SpecifiesItsCode;
use Sylius\Behat\Page\Admin\Crud\CreatePage as BaseCreatePage;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
class CreatePage extends BaseCreatePage implements CreatePageInterface
{
    use SpecifiesItsCode;

    /**
     * {@inheritdoc}
     */
    public function specifyPrice($price)
    {
        $this->getDocument()->fillField('Price', $price);
    }

    /**
     * {@inheritdoc}
     */
    public function nameIt($name)
    {
        $this->getDocument()->fillField('Presentation', $name);
    }

    /**
     * {@inheritdoc}
     */
    protected function getDefinedElements()
    {
        return array_merge(parent::getDefinedElements(), [
            'code' => '#sylius_product_variant_code',
            'price' => '#sylius_product_variant_price',
        ]);
    }
}
