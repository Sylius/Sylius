<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Mailer\Provider;

use Sylius\Mailer\Model\EmailInterface;

/**
 * Returns e-mail information.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
interface EmailProviderInterface
{
    /**
     * @param string $code
     *
     * @return EmailInterface
     */
    public function getEmail($code);
}
