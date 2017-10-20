<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Component\Mailer\Provider;

use Sylius\Component\Mailer\Model\EmailInterface;

interface EmailProviderInterface
{
    /**
     * @param string $code
     *
     * @return EmailInterface
     */
    public function getEmail(string $code): EmailInterface;
}
