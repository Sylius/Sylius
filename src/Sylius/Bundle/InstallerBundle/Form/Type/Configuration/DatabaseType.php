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

class DatabaseType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('sylius_database_driver', 'choice', array(
                'choices' => array(
                    'pdo_mysql'  => 'sylius.form.configuration.database.driver.mysql',
                    'pdo_pgsql'  => 'sylius.form.configuration.database.driver.postgresql',
                    'pdo_sqlite' => 'sylius.form.configuration.database.driver.sqlite',
                    'oci8'       => 'sylius.form.configuration.database.driver.oracle',
                    'pdo_sqlsrv' => 'sylius.form.configuration.database.driver.microsoft_sql_server',
                ),
                'constraints' => array(
                    new Assert\NotBlank(),
                ),
                'label' => 'sylius.form.configuration.database.driver',

            ))
            ->add('sylius_database_host', 'text', array(
                'data'  => '127.0.0.1',
                'constraints' => array(
                    new Assert\NotBlank(),
                ),
                'label' => 'sylius.form.configuration.database.host',
            ))
            ->add('sylius_database_port', 'integer', array(
                'required'    => false,
                'constraints' => array(
                    new Assert\Type(array('type' => 'integer'))
                ),
                'label'    => 'sylius.form.configuration.database.port'
            ))
            ->add('sylius_database_name', 'text', array(
                'constraints' => array(
                    new Assert\NotBlank(),
                ),
                'label' => 'sylius.form.configuration.database.name'
            ))
            ->add('sylius_database_user', 'text', array(
                'constraints' => array(
                    new Assert\NotBlank(),
                ),
                'label' => 'sylius.form.configuration.database.user'
            ))
            ->add('sylius_database_password', 'password', array(
                'required' => false,
                'label'    => 'sylius.form.configuration.database.password'
            ))
        ;
    }

    public function getName()
    {
        return 'sylius_configuration_database';
    }
}
