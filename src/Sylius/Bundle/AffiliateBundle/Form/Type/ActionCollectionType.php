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

use Sylius\Bundle\AffiliateBundle\Form\Type\Core\AbstractConfigurationCollectionType;

class ActionCollectionType extends AbstractConfigurationCollectionType
{
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sylius_affiliate_goal_action_collection';
    }

    /**
     * {@inheritdoc}
     */
    public function getFormTypeOption()
    {
        return 'sylius_affiliate_goal_action';
    }
}
