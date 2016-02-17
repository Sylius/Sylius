<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Form\Type;

use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Simple page type.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class PageType extends AbstractResourceType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('id', 'text', [
                'label' => 'sylius.form.page.id',
            ])
            ->add('title', 'text', [
                'label' => 'sylius.form.page.title',
            ])
            ->add('body', 'textarea', [
                'required' => false,
                'label' => 'sylius.form.page.body',
            ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sylius_page';
    }
}
