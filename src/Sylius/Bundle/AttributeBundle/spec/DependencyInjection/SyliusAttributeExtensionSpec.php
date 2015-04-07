<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\AttributeBundle\DependencyInjection;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\ResourceBundle\SyliusResourceBundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Prophecy\Argument;

/**
 * @author Adam Elsodaney <adam.elso@gmail.com>
 * @author Gonzalo Vilaseca <gvilaseca@reiss.co.uk>
 */
class SyliusAttributeExtensionSpec extends ObjectBehavior
{
    function it_processes_the_configuration_and_registers_services_per_subject(ContainerBuilder $container)
    {
        $container->hasParameter('sylius.translation.mapping')->willReturn(false);
        $container->hasParameter('sylius.translation.default.mapping')->willReturn(true);
        $container->getParameter('sylius.translation.default.mapping')->willReturn(
            array(
                array('default_mapping' => array(
                    'translatable' => array(
                        'field'          => 'translations',
                        'currentLocale'  => 'currentLocale',
                        'fallbackLocale' => 'fallbackLocale'
                    ),
                    'translation'  => array(
                        'field'  => 'translatable',
                        'locale' => 'locale'
                    )
                ))
            ));

        $attributeFormType = new Definition('Some\App\Product\Form\AttributeType');
        $attributeFormType
            ->setArguments(array('Some\App\Product\Entity\Attribute',
                '%sylius.validation_group.product_attribute%',
                'product'
            ))
        ;

        $attributeFormType
            ->addTag('form.type', array('alias' => 'sylius_product_attribute'))
        ;

        $container->setDefinition('sylius.form.type.product_attribute', $attributeFormType)->shouldBeCalled();

        $attributeTranslationFormType = new Definition('Some\App\Product\Form\AttributeTranslationType');
        $attributeTranslationFormType
            ->setArguments(array('Some\App\Product\Entity\AttributeTranslation',
                '%sylius.validation_group.product_attribute_translation%',
                'product'
            ))
        ;

        $attributeTranslationFormType
            ->addTag('form.type', array('alias' => 'sylius_product_attribute_translation'))
        ;

        $container->setDefinition('sylius.form.type.product_attribute_translation', $attributeTranslationFormType)->shouldBeCalled();

        $choiceTypeClasses = array(
            SyliusResourceBundle::DRIVER_DOCTRINE_ORM => 'Sylius\Bundle\AttributeBundle\Form\Type\AttributeEntityChoiceType'
        );

        $attributeChoiceFormType = new Definition($choiceTypeClasses[SyliusResourceBundle::DRIVER_DOCTRINE_ORM]);
        $attributeChoiceFormType
            ->setArguments(array('product', 'Some\App\Product\Entity\Attribute'))
        ;

        $attributeChoiceFormType
            ->addTag('form.type', array('alias' => 'sylius_product_attribute_choice'));
        ;

        $container->setDefinition('sylius.form.type.product_attribute_choice', $attributeChoiceFormType)->shouldBeCalled();

        $attributeValueFormType = new Definition('Some\App\Product\Form\AttributeValueType');
        $attributeValueFormType
            ->setArguments(array(
                'Some\App\Product\Entity\AttributeValue',
                '%sylius.validation_group.product_attribute_value%',
                'product'
            ))
        ;

        $attributeValueFormType
            ->addTag('form.type', array('alias' => 'sylius_product_attribute_value'))
        ;

        $container->setDefinition('sylius.form.type.product_attribute_value', $attributeValueFormType)->shouldBeCalled();

        $subjects = array(
            'product' => array(
                'subject' => 'Some\App\Product\Entity\Product',
                'attribute' => array(
                    'model' => 'Some\App\Product\Entity\Attribute',
                    'form'  => 'Some\App\Product\Form\AttributeType',
                    'translation' => array(
                        'model' => 'Some\App\Product\Entity\AttributeTranslation',
                        'form'  => array(
                            'default' => 'Some\App\Product\Form\AttributeTranslationType',
                        )
                    ),
                ),
                'attribute_value' => array(
                    'model' => 'Some\App\Product\Entity\AttributeValue',
                    'form'  => 'Some\App\Product\Form\AttributeValueType',
                ),
            ),
        );

        $container->setParameter('sylius.attribute.subjects', $subjects)->shouldBeCalled();

        $userConfig = array(
            'driver' => SyliusResourceBundle::DRIVER_DOCTRINE_ORM,
            'classes' => array(
                'product' => array(
                    'subject' => 'Some\App\Product\Entity\Product',
                    'attribute' => array(
                        'model' => 'Some\App\Product\Entity\Attribute',
                        'form' => 'Some\App\Product\Form\AttributeType',
                        'translation' => array(
                            'model' => 'Some\App\Product\Entity\AttributeTranslation',
                            'form' => array(
                                'default' => 'Some\App\Product\Form\AttributeTranslationType',
                            )
                        ),
                    ),
                    'attribute_value' => array(
                        'model' => 'Some\App\Product\Entity\AttributeValue',
                        'form' => 'Some\App\Product\Form\AttributeValueType',
                    ),
                ),
            ),
        );
        $processedConfig = array(
            'driver' => SyliusResourceBundle::DRIVER_DOCTRINE_ORM,
            'classes' => array(
                'product_attribute' => array(
                    'model' => 'Some\App\Product\Entity\Attribute',
                    'form'  => 'Some\App\Product\Form\AttributeType',
                    'translation' => array(
                        'model' => 'Some\App\Product\Entity\AttributeTranslation',
                        'form' => array(
                            'default' => 'Some\App\Product\Form\AttributeTranslationType',
                        )
                    ),
                ),
                'product_attribute_value' => array(
                    'model' => 'Some\App\Product\Entity\AttributeValue',
                    'form'  => 'Some\App\Product\Form\AttributeValueType',
                ),
            ),
            'validation_groups' => array(
                'product_attribute' => array('sylius'),
                'product_attribute_translation' => array('sylius'),
                'product_attribute_value' => array('sylius'),
            ),
        );
        $this->process($userConfig, $container)->shouldReturn($processedConfig);
    }
}
