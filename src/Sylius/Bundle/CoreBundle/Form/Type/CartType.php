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
use Sylius\Component\Resource\Repository\RepositoryInterface;
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
    protected $couponRepository;

    /**
     * Constructor.
     *
     * @param RepositoryInterface $couponRepository
     * @param string              $dataClass        FQCN of cart model
     * @param array               $validationGroups
     */
    public function __construct(RepositoryInterface $couponRepository, $dataClass, array $validationGroups)
    {
        $this->couponRepository = $couponRepository;

        parent::__construct($dataClass, $validationGroups);
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $couponRepository = $this->couponRepository;

        $builder
            ->add('promotionCoupons', 'collection', array(
                'type'         => 'sylius_promotion_coupon_to_code',
                'allow_add'    => true,
                'allow_delete' => true,
                'by_reference' => false,
                'label'        => 'sylius.form.cart.coupon',
            ))
            ->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) use ($couponRepository) {
                $data = $event->getData();

                if (!$data->getPromotionCoupons()->isEmpty()) {
                    return;
                }

                $data->addPromotionCoupon(
                    $couponRepository->createNew()
                );
            })
        ;
    }
}
