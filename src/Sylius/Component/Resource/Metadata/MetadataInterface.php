<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Resource\Metadata;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
interface MetadataInterface
{
    /**
     * @return string
     */
    public function getAlias();

    /**
     * @return string
     */
    public function getApplicationName();

    /**
     * @return string
     */
    public function getName();

    /**
     * @return string
     */
    public function getPluralName();

    /**
     * @return string
     */
    public function getDriver();

    /**
     * @return string
     */
    public function getTemplatesNamespace();

    /**
     * @param string $name
     *
     * @throws \InvalidArgumentException
     *
     * @return string|array
     */
    public function getParameter($name);

    /**
     * @param $name
     *
     * @return boolean
     */
    public function hasParameter($name);

    /**
     * @param string $name
     *
     * @throws \InvalidArgumentException
     *
     * @return string|array
     */
    public function getClass($name);

    /**
     * @param $name
     *
     * @return boolean
     */
    public function hasClass($name);

    /**
     * @param string $serviceName
     *
     * @return string
     */
    public function getServiceId($serviceName);

    /**
     * @param string $permissionName
     *
     * @return string
     */
    public function getPermissionCode($permissionName);
}
