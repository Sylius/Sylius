<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ChannelBundle\Form\Type;

use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Sylius channel form type.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class ChannelType extends AbstractResourceType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('code', 'text', array(
                'label'    => 'sylius.form.channel.code',
            ))
            ->add('name', 'text', array(
                'label' => 'sylius.form.channel.name',
            ))
            ->add('description', 'text', array(
                'label'    => 'sylius.form.channel.description',
                'required' => false,
            ))
            ->add('enabled', 'checkbox', array(
                'label'    => 'sylius.form.channel.enabled',
                'required' => false,
            ))
            ->add('url', 'text', array(
                'label'    => 'sylius.form.channel.url',
                'required' => false,
            ))
            ->add('color', 'text', array(
                'label'    => 'sylius.form.channel.color',
                'required' => false,
            ))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sylius_channel';
    }
}
