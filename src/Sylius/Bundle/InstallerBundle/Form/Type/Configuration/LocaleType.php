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
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints as Assert;

class LocaleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('sylius_locale', 'locale', array(
                'preferred_choices' => array('en', 'pl', 'es', 'de'),
                'constraints' => array(
                    new Assert\NotBlank(),
                    new Assert\Locale(),
                ),
                'label' => 'sylius.form.configuration.locale.locale',
            ))
            ->add('sylius_currency', 'choice', array(
                'choices' => array('EUR' => '€', 'USD' => '$'),
                'constraints' => array(
                    new Assert\NotBlank(),
                ),
                'label' => 'sylius.form.configuration.locale.currency',
            ))
        ;
    }

    public function getName()
    {
        return 'sylius_configuration_locale';
    }
}
