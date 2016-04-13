<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Page\Admin\ShippingMethod;

use Sylius\Behat\Behaviour\ChecksCodeImmutability;
use Sylius\Behat\Behaviour\Toggles;
use Sylius\Behat\Page\Admin\Crud\UpdatePage as BaseUpdatePage;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
class UpdatePage extends BaseUpdatePage implements UpdatePageInterface
{
    use ChecksCodeImmutability;
    use Toggles;

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
    protected function getToggleableElement()
    {
        return $this->getElement('enabled');
    }

    /**
     * {@inheritdoc}
     */
    protected function getDefinedElements()
    {
        return array_merge(parent::getDefinedElements(), [
            'code' => '#sylius_shipping_method_code',
            'enabled' => '#sylius_shipping_method_enabled',
            'name' => '#sylius_shipping_method_translations_en_US_name',
        ]);
    }
}
