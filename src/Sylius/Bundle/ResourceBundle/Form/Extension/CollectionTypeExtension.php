<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ResourceBundle\Form\Extension;

use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class CollectionTypeExtension extends AbstractTypeExtension
{
    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        if (array_key_exists('button_add_label', $options)) {
            $view->vars['button_add_label'] = $options['button_add_label'];
        }

        if (array_key_exists('item_by_line', $options)) {
            $view->vars['item_by_line'] = $options['item_by_line'];
        }
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setOptional(array(
            'button_add_label',
            'item_by_line',
        ));

        $resolver->setAllowedTypes(array(
            'item_by_line' => array('integer'),
        ));

        $resolver->setDefaults(array(
            'button_add_label' => 'form.collection.add',
            'item_by_line' => 1,
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
