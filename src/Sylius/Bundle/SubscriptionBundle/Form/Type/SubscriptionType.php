<?php

/*
* This file is part of the Sylius package.
*
* (c) Paweł Jędrzejewski
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Sylius\Bundle\SubscriptionBundle\Form\Type;


use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class SubscriptionType extends AbstractType
{
    /**
     * Subscription class name.
     *
     * @var string
     */
    protected $className;

    /**
     * @var EventSubscriberInterface
     */
    protected $subscriber;

    /**
     * Constructor.
     *
     * @param string $className
     */
    public function __construct($className, EventSubscriberInterface $subscriber)
    {
        $this->className = $className;
        $this->subscriber = $subscriber;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addEventSubscriber($this->subscriber);

        $builder
            ->add('scheduledDate', 'date', array(
                'label' => 'sylius.form.subscription.scheduled_date'
            ))
            ->add('items', 'collection', array(
                'type' => 'sylius_subscription_item',
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
                'data_class' => $this->className,
                'validation_groups' => array('sylius'),
                'cascade_validation' => true
            ))
        ;
    }

    public function getName()
    {
        return 'sylius_subscription';
    }
}