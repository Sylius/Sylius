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
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
final class ChannelType extends AbstractResourceType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->addEventSubscriber(new AddCodeFormSubscriber())
            ->add('name', TextType::class, [
                'label' => 'sylius.form.channel.name',
            ])
            ->add('description', TextareaType::class, [
                'label' => 'sylius.form.channel.description',
                'required' => false,
            ])
            ->add('enabled', CheckboxType::class, [
                'label' => 'sylius.form.channel.enabled',
                'required' => false,
            ])
            ->add('hostname', TextType::class, [
                'label' => 'sylius.form.channel.hostname',
                'required' => false,
            ])
            ->add('color', TextType::class, [
                'label' => 'sylius.form.channel.color',
                'required' => false,
            ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'sylius_channel';
    }
}
