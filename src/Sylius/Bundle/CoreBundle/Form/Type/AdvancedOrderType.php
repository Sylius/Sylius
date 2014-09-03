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

use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Currency\Provider\CurrencyProviderInterface;
use Symfony\Component\Form\FormBuilderInterface;

class AdvancedOrderType extends OrderType
{
    /**
     * @var CurrencyProviderInterface
     */
    protected $currencyProvider;

    public function __construct($dataClass, array $validationGroups = array(), CurrencyProviderInterface $currencyProvider)
    {
        parent::__construct($dataClass, $validationGroups);

        $this->currencyProvider = $currencyProvider;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $currencies = array();
        foreach ($this->currencyProvider->getAvailableCurrencies() as $currency) {
            $currencies[$currency->getCode()] = $currency->getName();
        }

        $builder
            ->add('currency', 'choice', array(
                'choices' => $currencies,
            ))
            ->add('promotionCoupons', 'collection', array(
                'type'         => 'sylius_promotion_coupon_to_code',
                'allow_add'    => true,
                'allow_delete' => true,
                'by_reference' => false,
                'label'        => 'sylius.form.order.coupons'
            ))
            ->add('state', 'choice', array(
                'choices' => array(
                    OrderInterface::STATE_CART        => 'sylius.order.state.checkout',
                    OrderInterface::STATE_CART_LOCKED => 'sylius.order.state.cart_locked',
                    OrderInterface::STATE_PENDING     => 'sylius.order.state.ordered',
                    OrderInterface::STATE_CONFIRMED   => 'sylius.order.state.order_confimed',
                    OrderInterface::STATE_SHIPPED     => 'sylius.order.state.shipped',
                    OrderInterface::STATE_ABANDONED   => 'sylius.order.state.abandoned',
                    OrderInterface::STATE_CANCELLED   => 'sylius.order.state.cancelled',
                    OrderInterface::STATE_RETURNED    => 'sylius.order.state.returned',
                ),
            ))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sylius_advanced_order';
    }
}
