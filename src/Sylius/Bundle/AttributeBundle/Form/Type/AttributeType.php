<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\AttributeBundle\Form\Type;

use Sylius\Bundle\AttributeBundle\Form\EventSubscriber\BuildAttributeFormSubscriber;
use Sylius\Bundle\ResourceBundle\Form\EventSubscriber\AddCodeFormSubscriber;
use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Sylius\Bundle\ResourceBundle\Form\Type\ResourceTranslationsType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 * @author Leszek Prabucki <leszek.prabucki@gmail.com>
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
class AttributeType extends AbstractResourceType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->addEventSubscriber(new BuildAttributeFormSubscriber($builder->getFormFactory()))
            ->addEventSubscriber(new AddCodeFormSubscriber())
            ->add('translations', ResourceTranslationsType::class, [
                'entry_type' => AttributeTranslationType::class,
                'label' => 'sylius.form.attribute.translations',
            ])
            ->add('type', AttributeTypeChoiceType::class, [
                'label' => 'sylius.form.attribute.type',
                'disabled' => true,
            ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'sylius_attribute';
    }
}
