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
use Sylius\Component\Resource\Manager\DomainManagerInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

/**
 * Cart form.
 *
 * @author Julien Janvier <j.janvier@gmail.com>
 */
class CartType extends BaseCartType
{
    protected $couponManager;

    /**
     * Constructor.
     *
     * @param string                 $dataClass        FQCN of cart model
     * @param string[]               $validationGroups
     * @param DomainManagerInterface $couponManager
     */
    public function __construct($dataClass, array $validationGroups, DomainManagerInterface $couponManager)
    {
        parent::__construct($dataClass, $validationGroups);

        $this->couponManager = $couponManager;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $couponManager = $this->couponManager;

        $builder
            ->add('promotionCoupons', 'collection', array(
                'type'         => 'sylius_promotion_coupon_to_code',
                'allow_add'    => true,
                'allow_delete' => true,
                'by_reference' => false,
                'label'        => 'sylius.form.cart.coupon',
            ))
            ->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) use ($couponManager) {
                $data = $event->getData();

                if (!$data->getPromotionCoupons()->isEmpty()) {
                    return;
                }

                $data->addPromotionCoupon(
                    $couponManager->createNew()
                );
            })
        ;
    }
}
