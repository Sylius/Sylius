<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\VariationBundle\DependencyInjection;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\ResourceBundle\SyliusResourceBundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Prophecy\Argument;

/**
 * @author Adam Elsodaney <adam.elso@gmail.com>
 * @author Gonzalo Vilaseca <gvilaseca@reiss.co.uk>
 */
class SyliusVariationExtensionSpec extends ObjectBehavior
{
    function it_processes_the_configuration_and_registers_services_per_variable(ContainerBuilder $container)
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

        $container->setParameter('sylius.translation.mapping', Argument::any())->shouldBeCalled();

        $variantFormType = new Definition('Some\App\Product\Form\ProductVariantType');
        $variantFormType
            ->setArguments(array('Some\App\Product\Entity\ProductVariant', '%sylius.validation_group.product_variant%', 'product'))
            ->addTag('form.type', array('alias' => 'sylius_product_variant'))
        ;

        $container->setDefinition('sylius.form.type.product_variant', $variantFormType)->shouldBeCalled();

        $variantChoiceFormType = new Definition('Sylius\Bundle\VariationBundle\Form\Type\VariantChoiceType');
        $variantChoiceFormType
            ->setArguments(array('product'))
            ->addTag('form.type', array('alias' => 'sylius_product_variant_choice'))
        ;

        $container->setDefinition('sylius.form.type.product_variant_choice', $variantChoiceFormType)->shouldBeCalled();

        $variantMatchFormType = new Definition('Sylius\Bundle\VariationBundle\Form\Type\VariantMatchType');
        $variantMatchFormType
            ->setArguments(array('product'))
            ->addTag('form.type', array('alias' => 'sylius_product_variant_match'))
        ;

        $container->setDefinition('sylius.form.type.product_variant_match', $variantMatchFormType)->shouldBeCalled();

        $optionFormType = new Definition('Some\App\Product\Form\OptionType');
        $optionFormType
            ->setArguments(array('Some\App\Product\Entity\Option',
                '%sylius.validation_group.product_option%',
                'product'
            ))
            ->addTag('form.type', array('alias' => 'sylius_product_option'))
        ;

        $container->setDefinition('sylius.form.type.product_option', $optionFormType)->shouldBeCalled();

        $optionTranslationFormType = new Definition('Some\App\Product\Form\OptionTranslationType');
        $optionTranslationFormType
            ->setArguments(array('Some\App\Product\Entity\OptionTranslation',
                '%sylius.validation_group.product_option_translation%',
                'product'
            ))
            ->addTag('form.type', array('alias' => 'sylius_product_option_translation'))
        ;

        $container->setDefinition('sylius.form.type.product_option_translation', $optionTranslationFormType)->shouldBeCalled();

        $optionChoiceFormType = new Definition('Sylius\Bundle\VariationBundle\Form\Type\OptionEntityChoiceType');
        $optionChoiceFormType
            ->setArguments(array('product', 'Some\App\Product\Entity\Option'))
            ->addTag('form.type', array('alias' => 'sylius_product_option_choice'));
        ;

        $container->setDefinition('sylius.form.type.product_option_choice', $optionChoiceFormType)->shouldBeCalled();

        $optionValueFormType = new Definition('Some\App\Product\Form\OptionValueType');
        $optionValueFormType
            ->setArguments(array(
                'Some\App\Product\Entity\OptionValue',
                '%sylius.validation_group.product_option_value%',
                'product'
            ))
            ->addTag('form.type', array('alias' => 'sylius_product_option_value'))
        ;

        $container->setDefinition('sylius.form.type.product_option_value', $optionValueFormType)->shouldBeCalled();

        $optionValueChoiceFormType = new Definition('Sylius\Bundle\VariationBundle\Form\Type\OptionValueChoiceType');
        $optionValueChoiceFormType
            ->setArguments(array('product'))
            ->addTag('form.type', array('alias' => 'sylius_product_option_value_choice'))
        ;

        $container->setDefinition('sylius.form.type.product_option_value_choice', $optionValueChoiceFormType)->shouldBeCalled();

        $optionValueCollectionFormType = new Definition('Sylius\Bundle\VariationBundle\Form\Type\OptionValueCollectionType');
        $optionValueCollectionFormType
            ->setArguments(array('product'))
            ->addTag('form.type', array('alias' => 'sylius_product_option_value_collection'))
        ;

        $container->setDefinition('sylius.form.type.product_option_value_collection', $optionValueCollectionFormType)->shouldBeCalled();


        $variables = array(
            'product' => array(
                'variable' => 'Some\App\Product\Entity\Product',
                'option' => array(
                    'model' => 'Some\App\Product\Entity\Option',
                    'form' => 'Some\App\Product\Form\OptionType',
                ),
                'option_translation' => array(
                    'model' => 'Some\App\Product\Entity\OptionTranslation',
                    'form' => 'Some\App\Product\Form\OptionTranslationType',
                ),
                'option_value' => array(
                    'model' => 'Some\App\Product\Entity\OptionValue',
                    'form' => 'Some\App\Product\Form\OptionValueType',
                ),
                'variant' => array(
                    'model' => 'Some\App\Product\Entity\ProductVariant',
                    'form' => 'Some\App\Product\Form\ProductVariantType',
                ),
            ),
        );
        $container->setParameter('sylius.variation.variables', $variables)->shouldBeCalled();

        $userConfig = array(
            'driver' => SyliusResourceBundle::DRIVER_DOCTRINE_ORM,
            'classes' => array(
                'product' => array(
                    'variable' => 'Some\App\Product\Entity\Product',
                    'option' => array(
                        'model' => 'Some\App\Product\Entity\Option',
                        'form' => 'Some\App\Product\Form\OptionType',
                    ),
                    'option_translation' => array(
                        'model' => 'Some\App\Product\Entity\OptionTranslation',
                        'form' => 'Some\App\Product\Form\OptionTranslationType',
                    ),
                    'option_value' => array(
                        'model' => 'Some\App\Product\Entity\OptionValue',
                        'form' => 'Some\App\Product\Form\OptionValueType',
                    ),
                    'variant' => array(
                        'model' => 'Some\App\Product\Entity\ProductVariant',
                        'form' => 'Some\App\Product\Form\ProductVariantType',
                    ),
                ),
            ),
        );
        $processedConfig = array(
            'driver' => SyliusResourceBundle::DRIVER_DOCTRINE_ORM,
            'classes' => array(
                'product_option' => array(
                    'model' => 'Some\App\Product\Entity\Option',
                    'form'  => 'Some\App\Product\Form\OptionType',
                ),
                'product_option_translation' => array(
                    'model' => 'Some\App\Product\Entity\OptionTranslation',
                    'form'  => 'Some\App\Product\Form\OptionTranslationType',
                ),
                'product_option_value' => array(
                    'model' => 'Some\App\Product\Entity\OptionValue',
                    'form'  => 'Some\App\Product\Form\OptionValueType',
                ),
                'product_variant' => array(
                    'model' => 'Some\App\Product\Entity\ProductVariant',
                    'form'  => 'Some\App\Product\Form\ProductVariantType',
                ),
            ),
            'validation_groups' => array(
                'product_variant' => array('sylius'),
                'product_option' => array('sylius'),
                'product_option_translation' => array('sylius'),
                'product_option_value' => array('sylius'),
            ),
        );
        $this->process($userConfig, $container)->shouldReturn($processedConfig);
    }
}
