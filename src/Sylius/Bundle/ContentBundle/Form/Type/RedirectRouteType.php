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
class RedirectRouteType extends AbstractResourceType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options = [])
    {
        $builder
            ->add('id', 'text', [
                'label' => 'sylius.form.redirect_route.id',
            ])
            ->add('name', 'text', [
                'label' => 'sylius.form.redirect_route.name',
            ])
            ->add('routeName', 'text', [
                'label' => 'sylius.form.redirect_route.route_name',
                'required' => false,
            ])
            ->add('uri', 'url', [
                'label' => 'sylius.form.redirect_route.uri',
                'required' => false,
            ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sylius_redirect_route';
    }
}
