<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Form\Type;

use Sylius\Bundle\AffiliateBundle\Form\Type\AffiliateGoalType as BaseAffiliateGoalType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * @author Laszlo Horvath <pentarim@gmail.com>
 */
class AffiliateGoalType extends BaseAffiliateGoalType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder
            ->add('channels', 'sylius_channel_choice', [
                'multiple' => true,
                'expanded' => true,
                'label' => 'sylius.form.affiliate.channels',
            ])
        ;
    }

}