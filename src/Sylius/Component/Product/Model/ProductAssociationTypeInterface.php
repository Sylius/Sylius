<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Product\Model;

use Sylius\Component\Resource\Model\CodeAwareInterface;
use Sylius\Component\Resource\Model\ResourceInterface;
use Sylius\Component\Resource\Model\TimestampableInterface;
use Sylius\Component\Resource\Model\TranslatableInterface;

/**
 * @author Leszek Prabucki <leszek.prabucki@gmail.com>
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
interface ProductAssociationTypeInterface extends
    CodeAwareInterface,
    TimestampableInterface,
    ResourceInterface,
    TranslatableInterface
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
