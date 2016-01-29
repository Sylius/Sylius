<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\MailerBundle\Form\Type;

use Sylius\Bundle\ResourceBundle\Form\EventSubscriber\AddCodeFormSubscriber;
use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class EmailType extends AbstractResourceType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->addEventSubscriber(new AddCodeFormSubscriber())
            ->add('enabled', 'checkbox', [
                'required' => false,
                'label' => 'sylius.form.email.enabled',
            ])
            ->add('senderName', 'text', [
                'label' => 'sylius.form.email.sender_name',
            ])
            ->add('senderAddress', 'email', [
                'label' => 'sylius.form.email.sender_address',
            ])
            ->add('subject', 'text', [
                'label' => 'sylius.form.email.subject',
            ])
            ->add('content', 'textarea', [
                'label' => 'sylius.form.email.content',
            ])
            ->add('template', 'sylius_email_template_choice', [
                'label' => 'sylius.form.email.template',
                'required' => false,
                'empty_value' => 'sylius.form.email.no_template',
            ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sylius_email';
    }
}
