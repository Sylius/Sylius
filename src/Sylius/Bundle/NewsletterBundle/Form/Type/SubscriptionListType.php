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
 * Sylius subscription list type.
 *
 * @author Michał Marcinkowski <michal.marcinkowski@lakion.com>
 */
class SubscriptionListType extends AbstractResourceType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', 'text', array(
                'label' => 'sylius.form.newsletter_subscription_list.name'
            ))
            ->add('description', 'textarea', array(
                'label' => 'sylius.form.newsletter_subscription_list.description'
            ))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sylius_newsletter_subscription_list';
    }
}
