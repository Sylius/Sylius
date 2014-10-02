<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\NewsletterBundle\Form\Type;

use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Sylius subscriber type.
 *
 * @author Michał Marcinkowski <michal.marcinkowski@lakion.com>
 */
class SubscriberType extends AbstractResourceType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', 'email', array(
                'label' => 'sylius.form.subscriber.email'
            ))
            ->add('subscription_lists', 'sylius_subscription_list_choice', array(
                'label'     => 'sylius.form.subscriber.subscription_lists',
                'multiple'  => 'true',
            ))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sylius_subscriber';
    }
}
