<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Page\Admin\ProductAttribute;

use Sylius\Behat\Behaviour\ChecksCodeImmutability;
use Sylius\Behat\Page\Admin\Crud\UpdatePage as BaseUpdatePage;

/**
 * @author Anna Walasek <anna.walasek@lakion.com>
 */
class UpdatePage extends BaseUpdatePage implements UpdatePageInterface
{
    use ChecksCodeImmutability;

    /**
     * {@inheritdoc}
     */
    public function changeName($name, $language)
    {
        $this->getDocument()->fillField(sprintf('sylius_product_attribute_translations_%s_name', $language), $name);
    }

    /**
     * {@inheritdoc}
     */
    public function isTypeDisabled()
    {
        return 'disabled' === $this->getElement('type')->getAttribute('disabled');
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
            'code' => '#sylius_product_attribute_code',
            'type' => '#sylius_product_attribute_type',
            'name' => '#sylius_product_attribute_translations_en_US_name',
        ]);
    }
}
