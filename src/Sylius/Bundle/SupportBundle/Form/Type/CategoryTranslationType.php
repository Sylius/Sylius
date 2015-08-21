<?php

namespace Sylius\Bundle\SupportBundle\Form\Type;

use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * @author Gustavo Perdomo <gperdomor@gmail.com>
 */
class CategoryTranslationType extends AbstractResourceType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', 'text', array('label' => 'sylius.form.support_category.title'));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sylius_support_category_translation';
    }
}
