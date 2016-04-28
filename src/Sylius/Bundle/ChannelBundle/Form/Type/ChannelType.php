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

use Sylius\Bundle\ResourceBundle\Form\EventSubscriber\AddCodeFormSubscriber;
use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Symfony\Component\Form\FormBuilderInterface;

/**
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
            ->addEventSubscriber(new AddCodeFormSubscriber())
            ->add('name', 'text', [
                'label' => 'sylius.form.channel.name',
            ])
            ->add('description', 'textarea', [
                'label' => 'sylius.form.channel.description',
                'required' => false,
            ])
            ->add('enabled', 'checkbox', [
                'label' => 'sylius.form.channel.enabled',
                'required' => false,
            ])
            ->add('hostname', 'text', [
                'label' => 'sylius.form.channel.hostname',
                'required' => false,
            ])
            ->add('color', 'text', [
                'label' => 'sylius.form.channel.color',
                'required' => false,
            ])
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
