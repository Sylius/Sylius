<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\AdminApiBundle\Model;

use FOS\OAuthServerBundle\Entity\AuthCode as BaseAuthCode;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class AuthCode extends BaseAuthCode implements AuthCodeInterface
{
}
