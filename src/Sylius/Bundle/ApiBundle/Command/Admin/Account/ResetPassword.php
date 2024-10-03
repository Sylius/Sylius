<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\ApiBundle\Command\Admin\Account;

use Sylius\Bundle\ApiBundle\Attribute\TokenAware;
use Sylius\Bundle\CoreBundle\Command\Admin\Account\ResetPassword as BaseResetPassword;

#[TokenAware]
class ResetPassword extends BaseResetPassword
{
}
