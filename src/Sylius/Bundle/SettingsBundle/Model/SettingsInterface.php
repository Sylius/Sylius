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
interface SettingsInterface extends ResourceInterface
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
     * @return Collection|ParameterInterface[]
     */
    public function getParameters();

    /**
     * @param string $name
     *
     * @throws \InvalidArgumentException
     *
     * @return ParameterInterface
     */
    public function getParameter($name);

    /**
     * @param string $name
     */
    public function hasParameter($name);

    /**
     * @param ParameterInterface $parameter
     */
    public function addParameter(ParameterInterface $parameter);

    /**
     * @param ParameterInterface $parameter
     */
    public function removeParameter(ParameterInterface $parameter);
}
