<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ContactBundle\Form\Type;

use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Sylius contact request form type.
 *
 * @author Michał Marcinkowski <michal.marcinkowski@lakion.com>
 */
class RequestType extends AbstractResourceType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstName', 'text', [
                'label' => 'sylius.form.contact_request.first_name',
            ])
            ->add('lastName', 'text', [
                'label' => 'sylius.form.contact_request.last_name',
            ])
            ->add('email', 'email', [
                'label' => 'sylius.form.contact_request.email',
            ])
            ->add('message', 'textarea', [
                'label' => 'sylius.form.contact_request.message',
            ])
            ->add('topic', 'sylius_contact_topic_choice', [
                'label' => 'sylius.form.contact_request.topic',
                'required' => false,
            ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sylius_contact_request';
    }
}
