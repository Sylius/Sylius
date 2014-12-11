<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\PromotionBundle\Form\Type;

use Sylius\Bundle\PromotionBundle\Form\Type\Core\AbstractConfigurationCollectionType;

/**
 * @author Arnaud Langlade <arn0d.dev@gmail.com>
 */
class RuleCollectionType extends AbstractConfigurationCollectionType
{
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sylius_promotion_rule_collection';
    }

    /**
     * {@inheritdoc}
     */
    public function getFormTypeOption()
    {
        return 'sylius_promotion_rule';
    }
}
