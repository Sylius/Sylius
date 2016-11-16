<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Form\Type\Promotion\Action;

use Sylius\Bundle\PromotionBundle\Form\Type\Action\FixedDiscountConfigurationType as BaseFixedDiscountConfigurationType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Type;

/**
 * @author Grzegorz Sadowski <grzegorz.sadowski@lakion.com>
 */
class FixedDiscountConfigurationType extends BaseFixedDiscountConfigurationType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder
            ->remove('amount')
            ->add('base_amount', 'sylius_money', [
                'label' => 'sylius.form.promotion_action.fixed_discount_configuration.base_amount',
                'constraints' => [
                    new NotBlank(),
                    new Type(['type' => 'numeric']),
                ],
            ])
            ->add('amounts', 'sylius_currency_based_discount_amount', [
                'required' => false,
            ])
        ;
    }
}
