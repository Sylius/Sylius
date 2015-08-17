<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\SeoBundle\Form\Type\Custom;

use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
class PageMetadataType extends AbstractResourceType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', 'text')
            ->add('description', 'textarea')
            ->add('keywords', 'textarea')
        ;

        $builder->get('keywords')->addModelTransformer(new CallbackTransformer(
            function ($originalKeywords) {
                if (!is_array($originalKeywords)) {
                    return '';
                }

                return join(', ', $originalKeywords);
            },
            function ($submittedKeywords) {
                $keywords = explode(',', $submittedKeywords);

                array_walk($keywords, function ($keyword) {
                    return trim($keyword);
                });

                return $keywords;
            }
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sylius_page_metadata';
    }
}