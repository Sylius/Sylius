<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\InstallerBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class SetupType extends AbstractType
{
    protected $dataClass;

    public function __construct($dataClass)
    {
        $this->dataClass = $dataClass;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username', TextType::class, array(
                'constraints' => array(
                    new Assert\NotBlank(),
                ),
                'label' => 'sylius.form.setup.username'
            ))
            ->add('plain_password', PasswordType::class, array(
                'constraints' => array(
                    new Assert\NotBlank(),
                ),
                'label' => 'sylius.form.setup.plain_password'
            ))
            ->add('email', EmailType::class, array(
                'constraints' => array(
                    new Assert\NotBlank(),
                    new Assert\Email(),
                ),
                'label' => 'sylius.form.setup.email'
            ))
            ->add('load_fixtures', CheckboxType::class, array(
                'required' => false,
                'mapped'   => false,
                'label'    => 'sylius.form.setup.load_fixtures'
            ))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults(array(
                'data_class' => $this->dataClass
            ))
        ;
    }

    public function getBlockPrefix()
    {
        return 'sylius_setup';
    }
}
