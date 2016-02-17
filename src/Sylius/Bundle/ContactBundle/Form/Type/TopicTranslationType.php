<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ContactBundle\Form\Type;

use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Contact topic translation form type.
 *
 * @author Gustavo Perdomo <gperdomor@gmail.com>
 */
class TopicTranslationType extends AbstractResourceType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', 'text', ['label' => 'sylius.form.contact_topic.title']);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sylius_contact_topic_translation';
    }
}
