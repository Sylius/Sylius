<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\MetadataBundle\Form\Type\Twitter;

use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
class AppCardType extends AbstractResourceType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('site', 'text')
            ->add('siteId', 'text')
            ->add('description', 'textarea')
            ->add('appNameIphone', 'text')
            ->add('appIdIphone', 'text')
            ->add('appUrlIphone', 'text')
            ->add('appNameIpad', 'text')
            ->add('appIdIpad', 'text')
            ->add('appUrlIpad', 'text')
            ->add('appNameGooglePlay', 'text')
            ->add('appIdGooglePlay', 'text')
            ->add('appUrlGooglePlay', 'text')
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sylius_twitter_app_card';
    }
}
