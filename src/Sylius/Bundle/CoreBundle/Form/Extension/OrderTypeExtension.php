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

use Sylius\Bundle\OrderBundle\Form\Type\OrderType;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 * @author Jan Góralski <jan.goralski@lakion.com>
 */
final class OrderTypeExtension extends AbstractTypeExtension
{
    /**
     * @var array
     */
    private $validationGroups;

    /**
     * @param array $validationGroups
     */
    public function __construct($validationGroups)
    {
        $this->validationGroups = $validationGroups;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('shippingAddress', 'sylius_address')
            ->add('billingAddress', 'sylius_address')
            ->add('promotionCoupon', 'sylius_promotion_coupon_to_code', [
                'by_reference' => false,
                'label' => 'sylius.form.cart.coupon',
                'required' => false,
            ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefault('validation_groups', function (FormInterface $form) {
            $validationGroups = $this->validationGroups;

            if (null !== $form->get('promotionCoupon')->getNormData()) { // Validate the coupon if it was sent
                $validationGroups[] = 'sylius_promotion_coupon';
            }

            return $validationGroups;
        });
    }

    /**
     * {@inheritdoc}
     */
    public function getExtendedType()
    {
        return OrderType::class;
    }
}
