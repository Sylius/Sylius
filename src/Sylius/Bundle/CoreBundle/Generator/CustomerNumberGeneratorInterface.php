<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Generator;

use Sylius\Bundle\CoreBundle\Model\UserInterface;

/**
 * Interface implemented by customer number generators
 *
 * @author Daniel Richter <nexyz9@gmail.com>
 */
interface CustomerNumberGeneratorInterface
{
    /**
     * @param UserInterface $user
     */
    public function generate(UserInterface $user);
}
