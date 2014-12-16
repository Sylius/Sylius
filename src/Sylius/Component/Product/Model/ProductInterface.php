<?php

/*
 * This file is part of the Sylius package.
 *
 * (c); Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Product\Model;

use Sylius\Component\Attribute\Model\AttributeSubjectInterface;
use Sylius\Component\Resource\Model\SoftDeletableInterface;
use Sylius\Component\Resource\Model\TimestampableInterface;
use Sylius\Component\Variation\Model\VariableInterface;

/**
 * Base product interface.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 * @author Gonzalo Vilaseca <gvilaseca@reiss.co.uk>
 */
interface ProductInterface extends
    AttributeSubjectInterface,
    VariableInterface,
    SoftDeletableInterface,
    TimestampableInterface,
    ProductTranslationInterface
{
    /**
     * Check whether the product is available.
     *
     * @return bool
     */
    public function isAvailable();

    /**
     * Return available on.
     *
     * @return \DateTime
     */
    public function getAvailableOn();

    /**
     * Set available on.
     *
     * @param null|\DateTime $availableOn
     */
    public function setAvailableOn(\DateTime $availableOn = null);
}
