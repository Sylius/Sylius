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
 * OAuth settings schema.
 *
 * @author Joseph Bielawski
 */
class OAuthSettingsSchema implements SchemaInterface
{
    /**
     * {@inheritdoc}
     */
    public function buildSettings(SettingsBuilderInterface $builder)
    {
        $builder
            ->setDefaults(array(
                'client_id'     => '',
                'client_secret' => '',
                'scope'         => '',
                'route'         => '',
            ))
            ->setAllowedTypes(array(
                'client_id'     => array('string'),
                'client_secret' => array('string'),
                'scope'         => array('string'),
                'route'         => array('string'),
            ))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder)
    {
        $builder
            ->add('client_id', 'text', array(
                'label'       => 'sylius.form.settings.oauth.client_id',
                'constraints' => array(
                    new NotBlank()
                )
            ))
            ->add('client_secret', 'text', array(
                'label'       => 'sylius.form.settings.oauth.client_secret',
                'constraints' => array(
                    new NotBlank()
                )
            ))
            ->add('route', 'text', array(
                'label'       => 'sylius.form.settings.oauth.route',
                'constraints' => array(
                    new NotBlank()
                )
            ))
            ->add('scope', 'text', array(
                'required'    => false,
                'label'       => 'sylius.form.settings.oauth.scope',
            ))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function isDynamic()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function getAlias()
    {
        return 'oauth';
    }
}
