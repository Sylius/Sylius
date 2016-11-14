<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Mailer\Factory;

use Sylius\Component\Mailer\Model\EmailInterface;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
interface EmailFactoryInterface
{
    /**
     * @return EmailInterface
     */
    public function createNew();
}
