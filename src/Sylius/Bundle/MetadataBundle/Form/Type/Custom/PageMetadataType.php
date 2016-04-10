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

use Sylius\Bundle\MetadataBundle\DynamicForm\DynamicFormBuilderInterface;
use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
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
     * @var DynamicFormBuilderInterface
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
            ->add('title', 'text', ['label' => 'sylius.metadata.page.title'])
            ->add('description', 'textarea', ['label' => 'sylius.metadata.page.description'])
            ->add('keywords', 'text', ['label' => 'sylius.metadata.page.keywords'])
        ;

        $this->dynamicFormBuilder->buildDynamicForm(
            $builder,
            'twitter',
            'sylius_twitter_card',
            [
                'select' => [
                    'label' => 'sylius.metadata.page.twitter',
                    'required' => false,
                    'placeholder' => 'sylius.metadata.type.none',
                ],
            ]
        );

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

                return implode(', ', $originalKeywords);
            },
            function ($submittedKeywords) {
                return array_map('trim', explode(',', $submittedKeywords));
            }
        ));
    }
}
