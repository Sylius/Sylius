<?php

namespace Sylius\Bundle\CoreBundle\Form\Type\Rule;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Type;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Sylius\Component\Core\Repository\ProductVariantRepositoryInterface;

use Sylius\Component\Order\Repository\OrderRepositoryInterface;

/**
 * Command loyalty rule configuration form type.
 *
 * @author Jean-Baptiste Blanchon <jean-baptiste@yproximite.com>
 */
class OrderLoyaltyConfigurationType extends AbstractType
{
    protected $validationGroups;

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nth', 'integer', array(
                'label' => 'sylius.form.rule.order_loyalty_configuration.nth',
                'constraints' => array(
                    new NotBlank(),
                    new Type(array('type' => 'numeric')),
                )
            ))
            ->add('unit', 'choice', array(
                'label'       => 'sylius.form.rule.order_loyalty_configuration.unit.header',
                'choices'     => array(
                    'days'   => 'sylius.form.rule.order_loyalty_configuration.unit.days',
                    'weeks'  => 'sylius.form.rule.order_loyalty_configuration.unit.weeks',
                    'months' => 'sylius.form.rule.order_loyalty_configuration.unit.months',
                    'years'  => 'sylius.form.rule.order_loyalty_configuration.unit.years',
                ),
                'constraints' => array(
                    new NotBlank(),
                )
            ))
            ->add('orderNth', 'integer', array(
                'label' => 'sylius.form.rule.order_loyalty_configuration.order_nth',
                'constraints' => array(
                    new Type(array('type' => 'numeric')),
                )
            ))
            ->add('after', 'checkbox', array(
                'label' => 'sylius.form.rule.order_loyalty_configuration.after',
            ))
            ->add('after', 'checkbox', array(
                'label' => 'sylius.form.rule.order_loyalty_configuration.after',
            ))
            ->add('equal', 'choice', array(
                'label'       => 'sylius.form.rule.order_loyalty_configuration.equal',
                'expanded'       => true,
                'multiple'       => false,
                'choices'     => array(
                    'up'   => 'sylius.form.rule.order_loyalty_configuration.up',
                    'down'  => 'sylius.form.rule.order_loyalty_configuration.down',
                ),
            ))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
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
        return 'sylius_promotion_rule_order_loyalty_configuration';
    }
}
