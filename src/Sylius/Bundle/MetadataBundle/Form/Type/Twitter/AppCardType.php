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
            ->add('description', 'textarea', ['label' => 'sylius.metadata.twitter.description'])
            ->add('site', 'text', ['label' => 'sylius.metadata.twitter.site'])
            ->add('siteId', 'text', ['label' => 'sylius.metadata.twitter.site_id'])
            ->add('appNameIphone', 'text', ['label' => 'sylius.metadata.twitter.app_name_iphone'])
            ->add('appIdIphone', 'text', ['label' => 'sylius.metadata.twitter.app_id_iphone'])
            ->add('appUrlIphone', 'text', ['label' => 'sylius.metadata.twitter.app_url_iphone'])
            ->add('appNameIpad', 'text', ['label' => 'sylius.metadata.twitter.app_name_ipad'])
            ->add('appIdIpad', 'text', ['label' => 'sylius.metadata.twitter.app_id_ipad'])
            ->add('appUrlIpad', 'text', ['label' => 'sylius.metadata.twitter.app_url_ipad'])
            ->add('appNameGooglePlay', 'text', ['label' => 'sylius.metadata.twitter.app_name_googleplay'])
            ->add('appIdGooglePlay', 'text', ['label' => 'sylius.metadata.twitter.app_id_googleplay'])
            ->add('appUrlGooglePlay', 'text', ['label' => 'sylius.metadata.twitter.app_url_googleplay'])
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
