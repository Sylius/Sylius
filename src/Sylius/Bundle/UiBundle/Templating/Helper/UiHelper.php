<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\PricingBundle\Templating\Helper;

use Symfony\Component\Templating\Helper\Helper;

/**
 * Sylius UI templating helper.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class UiHelper extends Helper
{
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sylius_ui';
    }
}
