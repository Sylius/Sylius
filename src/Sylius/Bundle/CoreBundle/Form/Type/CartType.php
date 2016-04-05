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

use Sylius\Bundle\CartBundle\Form\Type\CartType as BaseCartType;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

/**
 * @author Julien Janvier <j.janvier@gmail.com>
 */
class CartType extends BaseCartType
{
    protected $couponFactory;

    /**
     * @param string              $dataClass        FQCN of cart model
     * @param string[]            $validationGroups
     * @param FactoryInterface $couponFactory
     */
    public function __construct($dataClass, array $validationGroups, FactoryInterface $couponFactory)
    {
        parent::__construct($dataClass, $validationGroups);

        $this->couponFactory = $couponFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder
            ->add('promotionCoupon', 'sylius_promotion_coupon_to_code', [
                'by_reference' => false,
                'label' => 'sylius.form.cart.coupon',
                'required' => false,
            ])
            ->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
                $data = $event->getData();

                if (null !== $data->getPromotionCoupon()) {
                    return;
                }

                if ($event->getForm()->has('promotionCoupon')) {
                    $data->setPromotionCoupon(
                        $this->couponFactory->createNew()
                    );
                }
            })
        ;
    }
}
