<?php

/*
* This file is part of the Sylius package.
*
* (c) Paweł Jędrzejewski
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Sylius\Component\Core\Model;

use Sylius\Component\Affiliate\Model\AffiliateInterface as BaseAffiliateInterface;
use Sylius\Component\User\Model\CustomerAwareInterface;

/**
 * @author Laszlo Horvath <pentarim@gmail.com>
 */
interface AffiliateInterface extends BaseAffiliateInterface, CustomerAwareInterface
{
}
