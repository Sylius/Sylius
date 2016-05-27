<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\MetadataBundle\Form\Type;

use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * @author Pete Ward <peter.ward@reiss.com>
 */
class PageMetadataContainerTranslationType extends AbstractResourceType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('metadata', 'sylius_page_metadata', ['label' => false]);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sylius_page_metadata_container_translation';
    }
}