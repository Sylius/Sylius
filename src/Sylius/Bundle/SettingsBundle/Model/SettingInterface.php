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

/**
 * @author Steffen Brem <steffenbrem@gmail.com>
 */
interface SettingInterface
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
     * @return ParameterInterface
     */
    public function getParameter($name);

    /**
     * @param ParameterInterface $parameter
     */
    public function hasParameter(ParameterInterface $parameter);

    /**
     * @param ParameterInterface $parameter
     */
    public function addParameter(ParameterInterface $parameter);

    /**
     * @param ParameterInterface $parameter
     */
    public function removeParameter(ParameterInterface $parameter);
}
