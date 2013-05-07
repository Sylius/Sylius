<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Settings;

use Sylius\Bundle\SettingsBundle\Schema\SchemaInterface;
use Sylius\Bundle\SettingsBundle\Schema\SettingsBuilderInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * General settings schema.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class GeneralSettingsSchema implements SchemaInterface
{
    /**
     * {@inheritdoc}
     */
    public function buildSettings(SettingsBuilderInterface $builder)
    {
        $builder
            ->setDefaults(array(
                'title'            => 'Sklep - produkty',
                'meta_keywords'    => 'klucze, meta',
                'meta_description' => 'Sklep - opis meta'
            ))
            ->setAllowedTypes(array(
                'title'            => array('string'),
                'meta_keywords'    => array('string'),
                'meta_description' => array('string'),
            ))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder)
    {
        $builder
            ->add('title', 'text', array(
                'label'       => 'sylius.form.settings.general.title',
                'constraints' => array(
                    new NotBlank()
                )
            ))
            ->add('meta_keywords', 'text', array(
                'label'       => 'sylius.form.settings.general.meta_keywords',
                'constraints' => array(
                    new NotBlank()
                )
            ))
            ->add('meta_description', 'textarea', array(
                'label'       => 'sylius.form.settings.general.meta_description',
                'constraints' => array(
                    new NotBlank()
                )
            ))
        ;
    }
}
