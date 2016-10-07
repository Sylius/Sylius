<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\PromotionBundle\Form\Type\Action;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Range;
use Symfony\Component\Validator\Constraints\Type;

/**
 * @author Saša Stamenković <umpirsky@gmail.com>
 * @author Grzegorz Sadowski <grzegorz.sadowski@lakion.com>
 */
class PercentageDiscountConfigurationType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('percentage', 'percent', [
                'label' => 'sylius.form.action.percentage_discount_configuration.percentage',
                'constraints' => [
                    new NotBlank(),
                    new Type(['type' => 'numeric']),
                    new Range([
                        'min' => 0,
                        'max' => 1,
                        'minMessage' => 'sylius.action.percentage_discount_configuration.min',
                        'maxMessage' => 'sylius.action.percentage_discount_configuration.max',
                    ]),
                ],
            ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sylius_promotion_action_percentage_discount_configuration';
    }
}
