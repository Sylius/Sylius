<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Form\Type\Promotion;

use Sylius\Bundle\PromotionBundle\Form\Type\PromotionType as BasePromotionType;
use Sylius\Bundle\ResourceBundle\Form\Type\ResourceChoiceType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * @author Kristian Loevstroem <kristian@loevstroem.dk>
 */
class PromotionType extends BasePromotionType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder
            ->add('channels', ResourceChoiceType::class, [
                'resource' => 'sylius.channel',
                'multiple' => true,
                'expanded' => true,
                'label' => 'sylius.form.promotion.channels',
            ])
        ;
    }
}
