<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\NewsletterBundle\Form\Type;

/**
 * Subscription list choice type for "doctrine/orm" driver.
 *
 * @author Michał Marcinkowski <michal.marcinkowski@lakion.com>
 */
class SubscriptionListEntityType extends SubscriptionListChoiceType
{
    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return 'entity';
    }
}
