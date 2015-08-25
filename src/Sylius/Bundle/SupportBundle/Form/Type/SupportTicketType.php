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
use Sylius\Component\Support\Model\TicketInterface;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * @author Michał Marcinkowski <michal.marcinkowski@lakion.com>
 */
class SupportTicketType extends AbstractResourceType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstName', 'text', array(
                'label' => 'sylius.form.support_ticket.first_name'
            ))
            ->add('lastName', 'text', array(
                'label' => 'sylius.form.support_ticket.last_name'
            ))
            ->add('email', 'email', array(
                'label' => 'sylius.form.support_ticket.email'
            ))
            ->add('message', 'textarea', array(
                'label' => 'sylius.form.support_ticket.message'
            ))
            ->add('category', 'sylius_support_category_choice', array(
                'label'    => 'sylius.form.support_ticket.category',
                'required' => false,
            ))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sylius_support_ticket';
    }
}
