<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ContentBundle\Form\Type;

use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Menu block type.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class RouteType extends AbstractResourceType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options = [])
    {
        $builder
            ->add('name', null, [
                'label' => 'sylius.form.route.name',
            ])
            ->add('parent', null, [
                'label' => 'sylius.form.route.parent',
            ])
            ->add('content', 'sylius_static_content_choice', [
                'label' => 'sylius.form.route.content',
                'property' => 'title',
            ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sylius_route';
    }
}
