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
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Type;

/**
 * Class NthProductFixedDiscountConfigurationType
 *
 * @author Bruno Roux <bruno@yproximite.com>
 */
class NthProductFixedDiscountConfigurationType extends AbstractType
{
    /**
     * @var array
     */
    protected $validationGroups;

    /**
     * @param array $validationGroups
     */
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
                'label' => 'sylius.form.action.nth_product_fixed_discount_configuration.amount',
                'constraints' => array(
                    new NotBlank(),
                    new Type(array('type' => 'numeric')),
                ),
            ))
            ->add('nth', 'integer', array(
                'label' => 'sylius.form.action.nth_product_fixed_discount_configuration.nth',
                'constraints' => array(
                    new NotBlank(),
                    new Type(array('type' => 'numeric')),
                ),
            ))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults(array(
                'validation_groups' => $this->validationGroups,
            ))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sylius_promotion_action_nth_product_fixed_discount_configuration';
    }
}
