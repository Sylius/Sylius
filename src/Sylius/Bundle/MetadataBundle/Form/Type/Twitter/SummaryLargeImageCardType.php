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
class SummaryLargeImageCardType extends AbstractResourceType
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
            ->add('site', 'text', ['label' => 'sylius.metadata.twitter.site'])
            ->add('siteId', 'text', ['label' => 'sylius.metadata.twitter.site_id'])
            ->add('creator', 'text', ['label' => 'sylius.metadata.twitter.creator'])
            ->add('creatorId', 'text', ['label' => 'sylius.metadata.twitter.creator_id'])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sylius_twitter_summary_large_image_card';
    }
}
