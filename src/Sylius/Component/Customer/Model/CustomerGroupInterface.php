<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Customer\Model;

use Sylius\Component\Resource\Model\CodeAwareInterface;
use Sylius\Component\Resource\Model\ResourceInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
interface CustomerGroupInterface extends ResourceInterface, CodeAwareInterface
{
    /**
     * @return string
     */
    public function getName();

    /**
     * @param string $name
     */
    public function setName($name);
}
