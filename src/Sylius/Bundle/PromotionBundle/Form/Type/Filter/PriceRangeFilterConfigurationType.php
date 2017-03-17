<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\PromotionBundle\Form\Type\Filter;

use Sylius\Bundle\MoneyBundle\Form\Type\MoneyType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Type;

/**
 * @author Viorel Craescu <viorel@craescu.com>
 * @author Gabi Udrescu <gabriel.udr@gmail.com>
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
final class PriceRangeFilterConfigurationType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('min', MoneyType::class, [
                'required' => false,
                'constraints' => [
                    new Type(['type' => 'numeric', 'groups' => ['sylius']]),
                ],
                'currency' => $options['currency'],
            ])
            ->add('max', MoneyType::class, [
                'required' => false,
                'constraints' => [
                    new Type(['type' => 'numeric', 'groups' => ['sylius']]),
                ],
                'currency' => $options['currency'],
            ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setRequired('currency')
            ->setAllowedTypes('currency', 'string')
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'sylius_promotion_action_filter_price_range_configuration';
    }
}
