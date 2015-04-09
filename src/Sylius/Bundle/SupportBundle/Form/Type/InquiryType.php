<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\SupportBundle\Form\Type;

use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Sylius support inquiry form type.
 *
 * @author Michał Marcinkowski <michal.marcinkowski@lakion.com>
 */
class InquiryType extends AbstractResourceType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstName', 'text', array(
                'label' => 'sylius.form.support_inquiry.first_name',
            ))
            ->add('lastName', 'text', array(
                'label' => 'sylius.form.support_inquiry.last_name',
            ))
            ->add('email', 'email', array(
                'label' => 'sylius.form.support_inquiry.email',
            ))
            ->add('message', 'textarea', array(
                'label' => 'sylius.form.support_inquiry.message',
            ))
            ->add('topic', 'sylius_support_topic_choice', array(
                'label'    => 'sylius.form.support_inquiry.topic',
                'required' => false,
            ))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sylius_support_inquiry';
    }
}
