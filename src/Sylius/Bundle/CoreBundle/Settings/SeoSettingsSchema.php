<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Settings;

use Sylius\Bundle\SettingsBundle\Schema\SchemaInterface;
use Sylius\Bundle\SettingsBundle\Schema\SettingsBuilderInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * SEO settings schema.
 */
class SeoSettingsSchema implements SchemaInterface
{
    /**
     * {@inheritdoc}
     */
    public function buildSettings(SettingsBuilderInterface $builder)
    {
        $builder
            ->setDefaults(array(
                'global_title_formula'             => 'Sylius - Modern ecommerce for Symfony2',
                'global_meta_keywords_formula'     => 'symfony, sylius, ecommerce, webshop, shopping cart',
                'global_meta_description_formula'  => 'Sylius is modern ecommerce solution for PHP. Based on the Symfony2 framework.',
            ))
            ->setAllowedTypes(array(
                'global_title_formula'             => array('string'),
                'global_meta_keywords_formula'     => array('string'),
                'global_meta_description_formula'  => array('string'),
            ))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder)
    {
        $builder
            ->add('global_title_formula', 'text', $this->getFormOptions('global_title_formula'))
            ->add('global_meta_keywords_formula', 'text', $this->getFormOptions('global_meta_keywords_formula'))
            ->add('global_meta_description_formula', 'textarea', $this->getFormOptions('global_meta_description_formula'))
        ;
    }

    private function getFormOptions($name)
    {
        return array(
            'label'       => 'sylius.form.settings.seo.'.$name,
            'constraints' => array(
                new NotBlank(),
            )
        );
    }
}
