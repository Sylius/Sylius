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
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

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
            ->add('username', 'text', array(
                'constraints' => array(
                    new Assert\NotBlank(),
                ),
                'label' => 'sylius.form.setup.username'
            ))
            ->add('plain_password', 'password', array(
                'constraints' => array(
                    new Assert\NotBlank(),
                ),
                'label' => 'sylius.form.setup.plain_password'
            ))
            ->add('email', 'email', array(
                'constraints' => array(
                    new Assert\NotBlank(),
                    new Assert\Email(),
                ),
                'label' => 'sylius.form.setup.email'
            ))
            ->add('load_fixtures', 'checkbox', array(
                'required' => false,
                'mapped'   => false,
                'label'    => 'sylius.form.setup.load_fixtures'
            ))
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver
            ->setDefaults(array(
                'data_class' => $this->dataClass
            ))
        ;
    }

    public function getName()
    {
        return 'sylius_setup';
    }
}
