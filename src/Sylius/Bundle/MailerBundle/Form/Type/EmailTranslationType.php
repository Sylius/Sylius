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

use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * @author Manuel Gonzalez <mgonyan@gmail.com>
 */
class EmailTranslationType extends AbstractResourceType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('subject', 'text', array('label' => 'sylius.form.email.subject'))
            ->add('content', 'textarea', array('label' => 'sylius.form.email.content'));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sylius_email_translation';
    }
}
