<?php

/*
* This file is part of the Sylius package.
*
* (c) Paweł Jędrzejewski
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Sylius\Bundle\ReviewBundle\Doctrine\ORM;

use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Sylius\Bundle\ReviewBundle\Repository\ReviewRepositoryInterface;

/**
 * Subscription repository
 *
 * @author Daniel Richter <nexyz9@gmail.com>
 */
class ReviewRepository extends EntityRepository implements ReviewRepositoryInterface
{
}