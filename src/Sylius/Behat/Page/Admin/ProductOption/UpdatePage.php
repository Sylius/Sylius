<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Page\Admin\ProductOption;

use Sylius\Behat\Behaviour\ChecksCodeImmutability;
use Sylius\Behat\Page\Admin\Crud\UpdatePage as BaseUpdatePage;

/**
 * @author Grzegorz Sadowski <grzegorz.sadowski@lakion.com>
 */
class UpdatePage extends BaseUpdatePage implements UpdatePageInterface
{
    use ChecksCodeImmutability;

    /**
     * {@inheritdoc}
     */
    public function nameItIn($name, $language)
    {
        $this->getDocument()->fillField(
            sprintf('sylius_product_option_translations_%s_name', $language), $name
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function getCodeElement()
    {
        return $this->getElement('code');
    }

    /**
     * {@inheritdoc}
     */
    protected function getDefinedElements()
    {
        return array_merge(parent::getDefinedElements(), [
            'code' => '#sylius_product_option_code',
            'name' => '#sylius_product_option_translations_en_US_name',
            'values' => '#sylius_product_option_values',
        ]);
    }
}
