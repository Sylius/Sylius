<?php

namespace Sylius\Bundle\RbacBundle\Form\Type;

use Sylius\Bundle\ResourceBundle\Form\Type\ResourceChoiceType as BaseResourceChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ResourceChoiceType extends BaseResourceChoiceType
{
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver
            ->setDefaults([
                'query_builder' => function($er) {
                    return $er->createQueryBuilder('o')
                        ->addOrderBy('o.left', 'ASC')
                    ;
                },
            ])
        ;
    }
}
