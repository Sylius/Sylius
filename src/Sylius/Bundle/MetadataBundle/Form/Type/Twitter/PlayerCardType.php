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
class PlayerCardType extends AbstractResourceType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', 'text', ['label' => 'sylius.metadata.twitter.title'])
            ->add('description', 'textarea', ['label' => 'sylius.metadata.twitter.description'])
            ->add('image', 'text', ['label' => 'sylius.metadata.twitter.image'])
            ->add('player', 'textarea', ['label' => 'sylius.metadata.twitter.player'])
            ->add('site', 'text', ['label' => 'sylius.metadata.twitter.site'])
            ->add('siteId', 'text', ['label' => 'sylius.metadata.twitter.site_id'])
            ->add('playerWidth', 'number', ['label' => 'sylius.metadata.twitter.player_width'])
            ->add('playerHeight', 'number', ['label' => 'sylius.metadata.twitter.player_height'])
            ->add('playerStream', 'text', ['label' => 'sylius.metadata.twitter.player_stream'])
            ->add('playerStreamContentType', 'text', ['label' => 'sylius.metadata.twitter.player_stream_content_type'])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sylius_twitter_player_card';
    }
}
