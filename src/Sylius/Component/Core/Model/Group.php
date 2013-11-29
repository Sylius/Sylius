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

use FOS\UserBundle\Model\Group as BaseGroup;

/**
 * Group model.
 *
 * @author Paweł Jędrzjewski <pjedrzejewski@diweb.pl>
 */
class Group extends BaseGroup implements GroupInterface
{
    public function __construct()
    {
        $this->roles = array();
    }
}
