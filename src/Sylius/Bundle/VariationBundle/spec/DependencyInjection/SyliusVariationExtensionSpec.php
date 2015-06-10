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
        $container->hasParameter('sylius.translation.default_mapping')->willReturn(true);
        $container->getParameter('sylius.translation.default_mapping')->willReturn(
            array(
                array('mapping' => array(
                    'translatable' => array(
                        'translations'    => 'translations',
                        'current_locale'  => 'currentLocale',
                        'fallback_locale' => 'fallbackLocale'
                    ),
                    'translation'  => array(
                        'translatable' => 'translatable',
                        'locale'       => 'locale'
                    )
                ))
            )
        );

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
                    'form' => array(
                        'default' => 'Some\App\Product\Form\OptionType',
                    ),
                    'translation' => array(
                        'model' => 'Some\App\Product\Entity\OptionTranslation',
                        'form'  => array(
                            'default' => 'Some\App\Product\Form\OptionTranslationType',
                        )
                    )
                ),
                'option_value' => array(
                    'model' => 'Some\App\Product\Entity\OptionValue',
                    'form' => array(
                        'default' => 'Some\App\Product\Form\OptionValueType',
                    ),
                ),
                'variant' => array(
                    'model' => 'Some\App\Product\Entity\ProductVariant',
                    'form' => array(
                        'default' => 'Some\App\Product\Form\ProductVariantType',
                    ),
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
                        'form' => array(
                            'default' => 'Some\App\Product\Form\OptionType',
                        ),
                        'translation' => array(
                            'model' => 'Some\App\Product\Entity\OptionTranslation',
                            'form' => array(
                                'default' => 'Some\App\Product\Form\OptionTranslationType',
                            )
                        ),
                    ),
                    'option_value' => array(
                        'model' => 'Some\App\Product\Entity\OptionValue',
                        'form' => array(
                            'default' =>  'Some\App\Product\Form\OptionValueType',
                        ),
                    ),
                    'variant' => array(
                        'model' => 'Some\App\Product\Entity\ProductVariant',
                        'form' => array(
                            'default' => 'Some\App\Product\Form\ProductVariantType',
                        ),
                    ),
                ),
            ),
        );

        $processedConfig = array(
            'driver' => SyliusResourceBundle::DRIVER_DOCTRINE_ORM,
            'classes' => array(
                'product_option' => array(
                    'model' => 'Some\App\Product\Entity\Option',
                    'form' =>  array(
                        'default' => 'Some\App\Product\Form\OptionType',
                    ),
                    'translation' => array(
                        'model' => 'Some\App\Product\Entity\OptionTranslation',
                        'form'  => array(
                            'default' => 'Some\App\Product\Form\OptionTranslationType',
                        )
                    ),
                    'variable' => 'product',
                ),
                'product_option_value' => array(
                    'model' => 'Some\App\Product\Entity\OptionValue',
                    'form' =>  array(
                        'default' => 'Some\App\Product\Form\OptionValueType',
                    ),
                    'variable' => 'product',
                ),
                'product_variant' => array(
                    'model' => 'Some\App\Product\Entity\ProductVariant',
                    'form' => array(
                        'default' =>  'Some\App\Product\Form\ProductVariantType',
                    ),
                    'variable' => 'product',
                ),
            ),
            'validation_groups' => array(
                'product_variant'            => array('sylius'),
                'product_option'             => array('sylius'),
                'product_option_translation' => array('sylius'),
                'product_option_value'       => array('sylius'),
            ),
        );

        $this->process($userConfig, $container)->shouldReturn($processedConfig);
    }
}
