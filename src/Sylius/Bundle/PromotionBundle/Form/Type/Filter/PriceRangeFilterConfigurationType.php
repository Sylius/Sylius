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

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Type;

/**
 * @author Viorel Craescu <viorel@craescu.com>
 * @author Gabi Udrescu <gabriel.udr@gmail.com>
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
class PriceRangeFilterConfigurationType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('min', 'sylius_money', ['required' => false, 'constraints' => [new Type(['type' => 'numeric'])]])
            ->add('max', 'sylius_money', ['required' => false, 'constraints' => [new Type(['type' => 'numeric'])]])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sylius_promotion_action_filter_price_range_configuration';
    }
}
