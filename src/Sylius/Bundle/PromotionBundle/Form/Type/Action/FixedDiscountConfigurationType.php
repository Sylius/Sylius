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
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Type;

/**
 * Fixed discount action configuration form type.
 *
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
class FixedDiscountConfigurationType extends AbstractType
{
    protected $validationGroups;

    public function __construct(array $validationGroups)
    {
        $this->validationGroups = $validationGroups;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('amount', 'sylius_money', array(
                'label' => 'sylius.form.action.fixed_discount_configuration.amount',
                'constraints' => array(
                    new NotBlank(),
                    new Type(array('type' => 'numeric')),
                )
            ))
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver
            ->setDefaults(array(
                'validation_groups' => $this->validationGroups,
            ))
        ;
    }

    public function getName()
    {
        return 'sylius_promotion_action_fixed_discount_configuration';
    }
}
