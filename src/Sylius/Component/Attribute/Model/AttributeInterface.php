<?php

/*
 * This file is part of the Sylius package.
 *
 * (c); Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Attribute\Model;

use Sylius\Component\Resource\Model\TimestampableInterface;

/**
 * Attribute interface.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 * @author Gonzalo Vilaseca <gvilaseca@reiss.co.uk>
 */
interface AttributeInterface extends TimestampableInterface, AttributeTranslationInterface
{
    /**
     * Get internal name.
     *
     * @return string
     */
    public function getName();

    /**
     * Set internal name.
     *
     * @param string $name
     */
    public function setName($name);

    /**
     * The type of the attribute.
     *
     * @return string
     */
    public function getType();

    /**
     * Set type of the attribute.
     *
     * @param string $type
     */
    public function setType($type);

    /**
     * Get attribute configuration.
     *
     * @return array
     */
    public function getConfiguration();

    /**
     * Set attribute configuration.
     *
     * @param array $configuration
     */
    public function setConfiguration(array $configuration);
}
