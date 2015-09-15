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
use Sylius\Bundle\ReviewBundle\Form\Transformer\ReviewerTransformer;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * @author Daniel Richter <nexyz9@gmail.com>
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
class ReviewType extends AbstractResourceType
{
    /**
     * @var string
     */
    protected $subject;

    /**
     * @param string $dataClass
     * @param array  $validationGroups
     * @param string $subject
     */
    public function __construct($dataClass, array $validationGroups = array(), $subject)
    {
        $this->subject = $subject;

        parent::__construct($dataClass, $validationGroups);
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('rating', 'choice', array(
                'choices'  => $this->createRatingList($options['rating_steps']),
                'label'    => 'sylius.form.review.rating',
                'expanded' => true,
                'multiple' => false,
            ))
            ->add('author', 'text', array(
                'label'    => 'sylius.form.review.author',
                'required' => false,
            ))
            ->add('title', 'text', array(
                'label'    => 'sylius.form.review.title',
            ))
            ->add('comment', 'textarea', array(
                'label'    => 'sylius.form.review.comment',
            ))
        ;

        $builder->get('author')->addModelTransformer(new ReviewerTransformer());
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'rating_steps'      => 5,
            'validation_groups' => $this->validationGroups,
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return sprintf('sylius_%s_review', $this->subject);
    }

    /**
     * @param int $maxRate
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
