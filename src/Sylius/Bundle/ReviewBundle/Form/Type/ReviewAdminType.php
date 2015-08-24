<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ReviewBundle\Form\Type;

use Sylius\Component\Review\Model\ReviewInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * @author Daniel Richter <nexyz9@gmail.com>
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
class ReviewAdminType extends ReviewType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder->get('author')->resetModelTransformers();

        $builder
            ->remove('author')
            ->add('author', 'entity', array(
                'class' => 'Sylius\Component\Core\Model\Customer',
                'label' => 'sylius.form.review.author',
                'property' => 'email',
            ))
            ->add('status', 'choice', array(
                'choices' => array(
                    ReviewInterface::STATUS_NEW      => 'sylius.form.review.status.new',
                    ReviewInterface::STATUS_ACCEPTED => 'sylius.form.review.status.accepted',
                    ReviewInterface::STATUS_REJECTED => 'sylius.form.review.status.rejected'
                ),
                'label' => 'sylius.form.review.status.label'
            ))
            ->add('product', 'entity', array(
                'class'    => 'Sylius\Component\Core\Model\Product',
                'label'    => 'sylius.form.review.product',
                'property' => 'name',
            ))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'rating_steps'      => 5,
            'data_class'        => $this->dataClass,
            'validation_groups' => $this->validationGroups,
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sylius_review_admin';
    }
}
