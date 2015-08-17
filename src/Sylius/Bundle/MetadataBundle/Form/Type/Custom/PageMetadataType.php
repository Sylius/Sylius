<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\MetadataBundle\Form\Type\Custom;

use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Sylius\Bundle\MetadataBundle\DynamicForm\DynamicFormBuilderInterface;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
class PageMetadataType extends AbstractResourceType
{
    /**
     * @var DynamicFormBuilderInterface
     */
    private $dynamicFormBuilder;

    /**
     * {@inheritdoc}
     *
     * @var DynamicFormBuilderInterface $dynamicFormBuilder
     */
    public function __construct($dataClass, array $validationGroups = [], DynamicFormBuilderInterface $dynamicFormBuilder)
    {
        parent::__construct($dataClass, $validationGroups);

        $this->dynamicFormBuilder = $dynamicFormBuilder;
    }


    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', 'text')
            ->add('description', 'textarea')
            ->add('keywords', 'text')
        ;

        $this->dynamicFormBuilder->buildDynamicForm($builder, 'twitter', 'sylius_twitter_card');

        $this->addKeywordsTransformer($builder);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sylius_page_metadata';
    }

    /**
     * @param FormBuilderInterface $builder
     */
    private function addKeywordsTransformer(FormBuilderInterface $builder)
    {
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
}
