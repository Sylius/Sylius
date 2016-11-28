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

use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Valid;

/**
 * @author Daniel Richter <nexyz9@gmail.com>
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 * @author Grzegorz Sadowski <grzegorz.sadowski@lakion.com>
 */
abstract class ReviewType extends AbstractResourceType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('rating', ChoiceType::class, [
                'choices' => $this->createRatingList($options['rating_steps']),
                'label' => 'sylius.form.review.rating',
                'expanded' => true,
                'multiple' => false,
                'constraints' => [new Valid()],
            ])
            ->add('title', TextType::class, [
                'label' => 'sylius.form.review.title',
            ])
            ->add('comment', TextareaType::class, [
                'label' => 'sylius.form.review.comment',
            ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults([
            'rating_steps' => 5,
        ]);
    }

    /**
     * @param int $maxRate
     *
     * @return array
     */
    private function createRatingList($maxRate)
    {
        $ratings = [];
        for ($i = 1; $i <= $maxRate; ++$i) {
            $ratings[$i] = $i;
        }

        return $ratings;
    }
}
