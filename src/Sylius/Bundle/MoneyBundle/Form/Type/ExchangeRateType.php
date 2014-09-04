<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\MoneyBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Sylius exchange rate type.
 *
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
class ExchangeRateType extends AbstractType
{
    protected $dataClass;

    public function __construct($dataClass)
    {
        $this->dataClass = $dataClass;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('currency', 'currency', array(
                'label' => 'sylius.form.exchange_rate.currency'
            ))
            ->add('rate', 'text', array(
                'label' => 'sylius.form.exchange_rate.rate'
            ))
            ->add('baseRate', 'choice', array(
                    'label' => 'sylius.form.exchange_rate.base_rate',
                    'choices' => array(
                        0 =>'sylius.no',
                        1 => 'sylius.yes'
                    )
                )
            );
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver
            ->setDefaults(array(
                'data_class' => $this->dataClass
            ))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sylius_exchange_rate';
    }
}
