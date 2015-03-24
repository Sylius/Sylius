<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\AffiliateBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ActionChoiceType extends AbstractType
{
    protected $actions;

    public function __construct(array $actions)
    {
        $this->actions = $actions;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'choices' => $this->actions
        ));
    }

    public function getParent()
    {
        return 'choice';
    }

    public function getName()
    {
        return 'sylius_affiliate_goal_action_choice';
    }
}
