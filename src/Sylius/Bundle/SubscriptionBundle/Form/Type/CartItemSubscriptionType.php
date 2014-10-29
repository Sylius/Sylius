<?php

namespace Sylius\Bundle\SubscriptionBundle\Form\Type;

use Symfony\Component\Form\FormBuilderInterface;

/**
 * Simplified RecurringSubscriptionType for cart items.
 */
class CartItemSubscriptionType extends RecurringSubscriptionType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder
            ->remove('intervalUnit')
            ->add('intervalFrequency', 'integer', array(
                'label' => false,
                'required' => false,
            ))
            ->add('intervalUnit', 'choice', array(
                'choices' => self::$intervalChoices,
                'label' => false,
                'required' => false,
            ))
            ->remove('quantity')
            ->remove('scheduledDate')
            ->remove('maxCycles')
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sylius_cart_item_subscription';
    }
}
