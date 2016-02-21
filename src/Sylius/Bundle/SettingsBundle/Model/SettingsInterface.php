<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\SettingsBundle\Model;

use Doctrine\Common\Collections\Collection;
use Sylius\Component\Resource\Model\ResourceInterface;

/**
 * @author Steffen Brem <steffenbrem@gmail.com>
 */
interface SettingsInterface extends ResourceInterface, \IteratorAggregate, \ArrayAccess, \Countable
{
    /**
     * @return string
     */
    public function getSchema();

    /**
     * @param string $schema
     */
    public function setSchema($schema);

    /**
     * @return array
     */
    public function getParameters();

    /**
     * @param array $parameters
     */
    public function setParameters(array $parameters);

    /**
     * @param string $name
     *
     * @return ParameterInterface
     */
    public function get($name);

    /**
     * @param string $name
     */
    public function has($name);

    /**
     * @param string $name
     * @param mixed $value
     */
    public function set($name, $value);

    /**
     * @param $name
     */
    public function remove($name);
}
