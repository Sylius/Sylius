<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\PaymentBundle\Form\Type\Calculator;

use Sylius\Component\Currency\Context\CurrencyContext;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * @author Mateusz Zalewski <mateusz.p.zalewski@gmail.com>
 */
class FixedFeeCalculatorConfigurationType extends AbstractType
{
    /**
     * @var CurrencyContext
     */
    private $currencyContext;

    /**
     * Constructor.
     *
     * @param CurrencyContext $currencyContext
     */
    public function __construct(CurrencyContext $currencyContext)
    {
        $this->currencyContext = $currencyContext;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('amount', 'sylius_money', array(
                'label'    => 'sylius.form.payment_method.fee_calculator.fixed.amount',
                'currency' => $this->currencyContext->getCurrency(),
            ))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sylius_fee_calculator_fixed';
    }
}
