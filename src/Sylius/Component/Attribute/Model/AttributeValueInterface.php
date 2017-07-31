<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Component\Attribute\Model;

use Sylius\Component\Resource\Model\ResourceInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
interface AttributeValueInterface extends ResourceInterface
{
    public const STORAGE_BOOLEAN = 'boolean';
    public const STORAGE_DATE = 'date';
    public const STORAGE_DATETIME = 'datetime';
    public const STORAGE_FLOAT = 'float';
    public const STORAGE_INTEGER = 'integer';
    public const STORAGE_JSON = 'json';
    public const STORAGE_TEXT = 'text';

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
