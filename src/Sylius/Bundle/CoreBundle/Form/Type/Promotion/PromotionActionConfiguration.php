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

use Symfony\Component\Form\AbstractType;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
final class PromotionActionConfiguration extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sylius_promotion_action_configuration';
    }
}
