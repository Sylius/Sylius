<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Form\Extension;

use Sylius\Bundle\CurrencyBundle\Form\Type\CurrencyType;
use Sylius\Component\Currency\Model\CurrencyInterface;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

/**
 * @author Jan Góralski <jan.goralski@lakion.com>
 */
final class CurrencyTypeExtension extends AbstractTypeExtension
{
    /**
     * @var string
     */
    private $baseCurrency;

    /**
     * @param string $baseCurrency
     */
    public function __construct($baseCurrency)
    {
        $this->baseCurrency = $baseCurrency;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            /** @var CurrencyInterface $currency */
            $currency = $event->getData();
            if ($currency->getCode() !== $this->baseCurrency) {
                return;
            }

            $event
                ->getForm()
                ->add('enabled', CheckboxType::class, [
                    'label' => 'sylius.form.locale.enabled',
                    'disabled' => true,
                ])
                ->add('exchangeRate', NumberType::class, [
                    'label' => 'sylius.form.currency.exchange_rate',
                    'disabled' => true,
                ])
            ;
        });
    }

    /**
     * {@inheritdoc}
     */
    public function getExtendedType()
    {
        return CurrencyType::class;
    }
}
