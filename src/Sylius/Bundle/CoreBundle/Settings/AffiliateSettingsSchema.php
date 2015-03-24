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
use Sylius\Component\Affiliate\Model\AffiliateInterface;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * AffiliateGoal settings schema.
 *
 * @author Joseph Bielawski <stloyd@gmail.com>
 */
class AffiliateSettingsSchema implements SchemaInterface
{
    /**
     * {@inheritdoc}
     */
    public function buildSettings(SettingsBuilderInterface $builder)
    {
        $builder
            ->setDefaults(array(
                'enabled' => true,
                'provision_amount' => 1,
                'provision_type' => AffiliateInterface::PROVISION_PERCENT,
            ))
            ->setAllowedTypes(array(
                'enabled' => array('bool'),
                'provision_amount' => array('integer'),
                'provision_type' => array('integer'),
            ))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder)
    {
        $builder
            ->add('enabled', 'checkbox', array(
                'label' => 'sylius.form.settings.affiliate.enabled',
            ))
            ->add('provision_type', 'choice', array(
                'label' => 'sylius.form.settings.affiliate.provision_type',
                'choices' => array(
                    AffiliateInterface::PROVISION_FIXED => 'sylius.form.settings.affiliate.provision_fixed',
                    AffiliateInterface::PROVISION_PERCENT => 'sylius.form.settings.affiliate.provision_percent',
                ),
            ))
            ->add('provision_amount', 'number', array(
                'label' => 'sylius.form.settings.affiliate.provision_amount',
            ))
        ;
    }
}
