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
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Menu block type.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class RouteType extends AbstractResourceType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', null, array(
                'label' => 'sylius.form.route.name'
            ))
            ->add('parent', null, array(
                'label' => 'sylius.form.route.parent'
            ))
            ->add('content', null, array(
                'class' => 'Symfony\Cmf\Bundle\ContentBundle\Model\StaticContent',
                'property' => 'title',
                'label' => 'sylius.form.route.content',
                'required' => false
            ))
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
