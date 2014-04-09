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
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
interface AttributeInterface extends TimestampableInterface
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
     * The name displayed to user.
     *
     * @return string
     */
    public function getPresentation();

    /**
     * Set presentation.
     *
     * @param string $presentation
     */
    public function setPresentation($presentation);

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
