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

use Sylius\Bundle\PromotionBundle\Form\Type\CouponType;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * @author Myke Hines <myke@webhines.com>
 * @author Jan Góralski <jan.goralski@lakion.com>
 */
final class CouponTypeExtension extends AbstractTypeExtension
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('perCustomerUsageLimit', 'integer', [
                'label' => 'sylius.form.coupon.per_customer_usage_limit',
                'required' => false,
            ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getExtendedType()
    {
        return CouponType::class;
    }
}
