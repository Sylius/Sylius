<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Page\Admin\PaymentMethod;

use Behat\Mink\Element\NodeElement;
use Behat\Mink\Exception\ElementNotFoundException;
use Sylius\Behat\Behaviour\ChecksCodeImmutability;
use Sylius\Behat\Behaviour\Toggles;
use Sylius\Behat\Page\Admin\Crud\UpdatePage as BaseUpdatePage;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
class UpdatePage extends BaseUpdatePage implements UpdatePageInterface
{
    use ChecksCodeImmutability;
    use Toggles;

    /**
     * {@inheritdoc}
     */
    public function chooseGateway($gateway)
    {
        $this->getElement('gateway')->selectOption($gateway);
    }

    /**
     * {@inheritdoc}
     */
    public function nameIt($name, $languageCode)
    {
        $this->getDocument()->fillField(sprintf('sylius_payment_method_translations_%s_name', $languageCode), $name);
    }

    /**
     * {@inheritdoc}
     */
    public function isPaymentMethodEnabled()
    {
        return (bool) $this->getToggleableElement()->getValue();
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
            'code' => '#sylius_payment_method_code',
            'name' => '#sylius_payment_method_translations_en_US_name',
            'gateway' => '#sylius_payment_method_gateway',
            'enabled' => '#sylius_payment_method_enabled',
        ]);
    }
}
