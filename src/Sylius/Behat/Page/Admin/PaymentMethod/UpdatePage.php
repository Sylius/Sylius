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
use Sylius\Behat\Behaviour\ChecksCodeImmutability;
use Sylius\Behat\Behaviour\Toggles;
use Sylius\Behat\Page\Admin\Crud\UpdatePage as BaseUpdatePage;
use Sylius\Behat\Page\ElementNotFoundException;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
class UpdatePage extends BaseUpdatePage implements UpdatePageInterface
{
    use ChecksCodeImmutability, Toggles;

    /**
     * @var array
     */
    protected $elements = [
        'code' => '#sylius_payment_method_code',
        'name' => '#sylius_payment_method_translations_en_name',
        'gateway' => '#sylius_payment_method_gateway',
        'enabled' => '#sylius_payment_method_enabled',
    ];

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
    public function nameIt($name, $language)
    {
        $this->getDocument()->fillField(sprintf('sylius_payment_method_translations_%s_name', $language), $name);
    }

    /**
     * {@inheritdoc}
     */
    public function isPaymentMethodEnabled()
    {
        return (bool) $this->getToggleableElement()->getValue();
    }

    /**
     * @return NodeElement
     *
     * @throws ElementNotFoundException
     */
    protected function getCodeElement()
    {
        return $this->getElement('code');
    }

    /**
     * @return NodeElement
     *
     * @throws ElementNotFoundException
     */
    protected function getToggleableElement()
    {
        return $this->getElement('enabled');
    }
}
