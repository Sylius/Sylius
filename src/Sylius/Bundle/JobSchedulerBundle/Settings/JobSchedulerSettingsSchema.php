<?php
/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\JobSchedulerBundle\Settings;

use Sylius\Bundle\SettingsBundle\Schema\SchemaInterface;
use Sylius\Bundle\SettingsBundle\Schema\SettingsBuilderInterface;
use Symfony\Component\Form\FormBuilderInterface;


/**
 * Clas@author Gonzalo Vilaseca <gvilaseca@reiss.co.uk>@author Gonzalo Vilaseca
 */
class JobSchedulerSettingsSchema implements SchemaInterface
{
    /**
     * @param SettingsBuilderInterface $builder
     */
    public function buildSettings(SettingsBuilderInterface $builder)
    {
        $builder
            ->setDefaults(array(
                'enabled' => true,
            ));
    }

    /**
     * @param FormBuilderInterface $builder
     */
    public function buildForm(FormBuilderInterface $builder)
    {
        $builder
            ->add('enabled', 'checkbox', array('required' => false,));

    }
}
