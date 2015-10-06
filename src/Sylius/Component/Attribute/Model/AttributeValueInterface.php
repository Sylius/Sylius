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

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
interface AttributeValueInterface
{
    /**
     * @return mixed
     */
    public function getId();

    /**
     * @return AttributeSubjectInterface
     */
    public function getSubject();

    /**
     * @param AttributeSubjectInterface|null $subject
     */
    public function setSubject(AttributeSubjectInterface $subject = null);

    /**
     * @return AttributeInterface
     */
    public function getAttribute();

    /**
     * @param AttributeInterface $attribute
     */
    public function setAttribute(AttributeInterface $attribute);

    /**
     * @return mixed
     */
    public function getValue();

    /**
     * @param mixed $value
     */
    public function setValue($value);

    /**
     * Proxy method to access the name from real attribute.
     *
     * @return string
     */
    public function getName();

    /**
     * Proxy method to access the presentation from real attribute.
     *
     * @return string
     */
    public function getPresentation();

    /**
     * @return string
     */
    public function getType();

    /**
     * @return array
     */
    public function getConfiguration();
}
