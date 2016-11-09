<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Form\Type;

use Sylius\Bundle\CurrencyBundle\Form\Type\CurrencyType as BaseCurrencyType;
use Sylius\Component\Currency\Model\CurrencyInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

/**
 * @author Jan Góralski <jan.goralski@lakion.com>
 */
class CurrencyType extends BaseCurrencyType
{
    /**
     * @var string
     */
    private $baseCurrency;

    /**
     * {@inheritdoc}
     *
     * @param string $baseCurrency
     */
    public function __construct($dataClass, array $validationGroups = [], $baseCurrency)
    {
        parent::__construct($dataClass, $validationGroups);

        $this->baseCurrency = $baseCurrency;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            /** @var CurrencyInterface $currency */
            $currency = $event->getData();
            if ($currency->getCode() !== $this->baseCurrency) {
                return;
            }

            $form = $event->getForm();

            $form
                ->add('enabled', 'checkbox', [
                    'label' => 'sylius.form.locale.enabled',
                    'disabled' => true,
                ])
                ->add('exchangeRate', 'number', [
                    'label' => 'sylius.form.currency.exchange_rate',
                    'disabled' => true,
                ])
            ;
        });
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sylius_currency';
    }
}
