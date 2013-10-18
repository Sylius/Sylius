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

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * ReviewType
 *
 * @author Daniel Richter <nexyz9@gmail.com>
 */
class ReviewType extends AbstractType
{
    protected $dataClass;

    public function __construct($dataClass)
    {
        $this->dataClass = $dataClass;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $ratingRange = range(1, $options['rating_steps']);

        $builder
            ->add('rating', 'choice', array(
                'choices' => array_combine($ratingRange, $ratingRange),
                'empty_value' => 'sylius.form.review.rating.empty_value',
                'label' => 'sylius.form.review.rating.label'
            ))
            ->add('title', 'text', array(
                'label' => 'sylius.form.review.title.label'
            ))
            ->add('comment', 'textarea', array(
                'label' => 'sylius.form.review.comment.label'
            ));

        if ($options['allow_guest']) {
            $builder->add('guest_reviewer', 'sylius_guest_reviewer');
        }
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'rating_steps' => 5,
            'data_class' => $this->dataClass,
            'allow_guest' => false
        ));
    }

    public function getName()
    {
        return 'sylius_review';
    }
}
