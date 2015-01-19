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
 * Object describing a single resource.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
interface ResourceMetadataInterface
{
    /**
     * Returns the name of the resource itself. For example:
     *
     * - product
     * - user
     * - order_item
     * - shipping_method
     *
     * @return string
     */
    public function getResourceName();

    /**
     * Get plural resource name.
     *
     * @return string
     */
    public function getPluralResourceName();

    /**
     * Returns the name of application. For example:
     *
     * - app
     * - sylius
     * - acme
     *
     * @return string
     */
    public function getApplicationName();

    /**
     * Returns the full name of the resource, which is application and resource
     * names joint using a ".". (dot) For example:
     *
     * - app.product
     * - acme.shipping_method
     * - sylius.payment
     *
     * @return string
     */
    public function getAlias();

    /**
     * Return the driver.
     *
     * @return string
     */
    public function getDriver();

    /**
     * Get all parameters.
     *
     * @return array
     */
    public function getParameters();

    /**
     * Returns the parameter of given name.
     *
     * @param string $name
     * @param null   $default
     *
     * @return string
     */
    public function getParameter($name, $default = null);

    /**
     * Has parameter defined?
     *
     * @param string $name
     *
     * @return bool
     */
    public function hasParameter($name);

    /**
     * Get all classes.
     *
     * @return array
     */
    public function getClasses();

    /**
     * Returns the class of given type. Type can be, for example:
     *
     * - model
     * - repository
     * - controller
     * - factory
     * - form.default
     * - form.special
     *
     * @param string $type
     *
     * @return string|array
     */
    public function getClass($type);

    /**
     * Has class defined?
     *
     * @param string $type
     *
     * @return bool
     */
    public function hasClass($type);

    /**
     * Get the default templates namespace.
     *
     * @return string
     */
    public function getTemplatesNamespace();

    /**
     * Is resource timestampable?
     *
     * @return boolean
     */
    public function isTimestampable();

    /**
     * Is resource translatable?
     *
     * @return boolean
     */
    public function isTranslatable();

    /**
     * Is softdeleteable?
     *
     * @return boolean
     */
    public function isSoftdeleteable();

    /**
     * Return the service name.
     *
     * @param string $service
     *
     * @return string
     */
    public function getServiceId($service);
}
