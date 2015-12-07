<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\InstallerBundle\Form\Type\Configuration;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints as Assert;

class MailerType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('sylius_mailer_transport', ChoiceType::class, array(
                'choices' => array(
                    'smtp'     => 'sylius.form.configuration.mailer.transport.smtp',
                    'gmail'    => 'sylius.form.configuration.mailer.transport.gmail',
                    'mail'     => 'sylius.form.configuration.mailer.transport.mail',
                    'sendmail' => 'sylius.form.configuration.mailer.transport.sendmail',
                ),
                'constraints' => array(
                    new Assert\NotBlank(),
                ),
                'label' => 'sylius.form.configuration.mailer.transport',
            ))
            ->add('sylius_mailer_host', TextType::class, array(
                'data'  => '127.0.0.1',
                'constraints' => array(
                    new Assert\NotBlank(),
                ),
                'label' => 'sylius.form.configuration.mailer.host',
            ))
            ->add('sylius_mailer_user', TextType::class, array(
                'label'    => 'sylius.form.configuration.mailer.user',
                'required' => false,
            ))
            ->add('sylius_mailer_password', PasswordType::class, array(
                'required' => false,
                'label'    => 'sylius.form.configuration.mailer.password'
            ))
        ;
    }

    public function getBlockPrefix()
    {
        return 'sylius_configuration_mailer';
    }
}
