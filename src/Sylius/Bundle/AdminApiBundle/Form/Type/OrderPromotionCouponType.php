<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\AdminApiBundle\Form\Type;

use Sylius\Bundle\ChannelBundle\Form\Type\ChannelChoiceType;
use Sylius\Bundle\CustomerBundle\Form\Type\CustomerChoiceType;
use Sylius\Bundle\LocaleBundle\Form\Type\LocaleChoiceType;
use Sylius\Bundle\PromotionBundle\Form\Type\PromotionCouponToCodeType;
use Sylius\Bundle\ResourceBundle\Form\DataTransformer\ResourceToIdentifierTransformer;
use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\ReversedTransformer;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
final class OrderPromotionCouponType extends AbstractResourceType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('promotionCoupon', PromotionCouponToCodeType::class, [
                'by_reference' => false,
                'label' => 'sylius.form.cart.coupon',
                'required' => false,
            ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'sylius_admin_api_order_promotion_coupon';
    }
}
