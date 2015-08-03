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
 * @author Daniel Richter <nexyz9@gmail.com>
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
class ReviewType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('rating', 'choice', array(
                'choices' => $this->createRatingList($options['rating_steps']),
                'label' => 'sylius.form.review.rating.label',
                'expanded' => true,
                'multiple' => false,
            ))
            ->add('title', 'text', array(
                'label' => 'sylius.form.review.title.label'
            ))
            ->add('comment', 'textarea', array(
                'label' => 'sylius.form.review.comment.label'
            ))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'rating_steps' => 5,
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sylius_review';
    }

    /**
     * @param integer $maxRate
     *
     * @return array
     */
    private function createRatingList($maxRate)
    {
        $ratings = array();
        for ($i = 1; $i <= $maxRate; $i++) {
            $ratings[$i] = $i;
        }

        return $ratings;
    }
}
