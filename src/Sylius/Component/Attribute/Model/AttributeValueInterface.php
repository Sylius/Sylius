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
    const STORAGE_BOOLEAN = 'boolean';
    const STORAGE_DATE = 'date';
    const STORAGE_DATETIME = 'datetime';
    const STORAGE_FLOAT = 'float';
    const STORAGE_INTEGER = 'integer';
    const STORAGE_JSON = 'json';
    const STORAGE_TEXT = 'text';

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
     * @return string
     */
    public function getCode();

    /**
     * @return string
     */
    public function getName();

    /**
     * @return string
     */
    public function getType();

    /**
     * @return string
     *
     * @throws \InvalidArgumentException
     */
    public function getLocaleCode();

    /**
     * @param string
     */
    public function setLocaleCode($localeCode);
}
