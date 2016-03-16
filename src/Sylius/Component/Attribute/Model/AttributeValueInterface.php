<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Attribute\Model;

use Sylius\Component\Resource\Model\ResourceInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
interface AttributeValueInterface extends ResourceInterface
{
    const STORAGE_TEXT = 'text';
    const STORAGE_BOOLEAN = 'boolean';
    const STORAGE_DATE = 'date';
    const STORAGE_DATETIME = 'datetime';
    const STORAGE_INTEGER = 'integer';
    const STORAGE_FLOAT = 'float';

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
     * Proxy method to access the code from real attribute.
     *
     * @return string
     */
    public function getCode();

    /**
     * Proxy method to access the name from real attribute.
     *
     * @return string
     */
    public function getName();

    /**
     * @return string
     */
    public function getType();
}
