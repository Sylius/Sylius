<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Mailer\Provider;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
interface DefaultSettingsProviderInterface
{
    /**
     * @return string
     */
    public function getSenderName();

    /**
     * @return string
     */
    public function getSenderAddress();
}
