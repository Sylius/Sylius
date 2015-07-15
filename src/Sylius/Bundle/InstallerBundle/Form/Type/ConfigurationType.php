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

class ConfigurationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('database', 'sylius_configuration_database', array(
                'label' => 'sylius.form.configuration.database'
            ))
            ->add('mailer', 'sylius_configuration_mailer', array(
                'label' => 'sylius.form.configuration.mailer'
            ))
            ->add('locale', 'sylius_configuration_locale', array(
                'label' => 'sylius.form.configuration.locale'
            ))
            ->add('hidden', 'sylius_configuration_hidden')
        ;
    }

    public function getName()
    {
        return 'sylius_configuration';
    }
}
