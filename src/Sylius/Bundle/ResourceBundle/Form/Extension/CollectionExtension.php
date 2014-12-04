<?php

/*
 * This file is part of the NIM package.
 *
 * (c) Langlade Arnaud
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ResourceBundle\Form\Extension;

use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * @author Arnaud Langlade <arn0d.dev@gmail.com>
 */
class CollectionExtension extends AbstractTypeExtension
{
    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        if (array_key_exists('button_add_label', $options)) {
            $view->vars['button_add_label'] = $options['button_add_label'];
        }

        if (array_key_exists('button_delete_label', $options)) {
            $view->vars['button_delete_label'] = $options['button_delete_label'];
        }
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setOptional(array(
            'button_add_label',
            'button_delete_label',
        ));

        $resolver->setDefaults(array(
            'button_add_label' => 'form.collection.add',
            'button_delete_label' => 'form.collection.delete',
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getExtendedType()
    {
        return 'collection';
    }
}
