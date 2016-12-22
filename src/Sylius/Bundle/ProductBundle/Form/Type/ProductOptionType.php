<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ProductBundle\Form\Type;

use Sylius\Bundle\ResourceBundle\Form\EventSubscriber\AddCodeFormSubscriber;
use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Sylius\Bundle\ResourceBundle\Form\Type\ResourceTranslationsType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 * @author Gonzalo Vilaseca <gvilaseca@reiss.co.uk>
 */
final class ProductOptionType extends AbstractResourceType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('position', IntegerType::class, [
                'required' => false,
                'label' => 'sylius.form.option.position',
            ])
            ->add('translations', ResourceTranslationsType::class, [
                'entry_type' => ProductOptionTranslationType::class,
                'label' => 'sylius.form.option.name',
            ])
            ->add('values', CollectionType::class, [
                'entry_type' => ProductOptionValueType::class,
                'allow_add' => true,
                'by_reference' => false,
                'label' => false,
                'button_add_label' => 'sylius.form.option_value.add_value',
            ])
            ->addEventSubscriber(new AddCodeFormSubscriber())
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'sylius_product_option';
    }
}
