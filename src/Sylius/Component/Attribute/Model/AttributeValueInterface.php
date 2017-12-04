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
     * @return AttributeSubjectInterface|null
     */
    public function getSubject(): ?AttributeSubjectInterface;

    /**
     * @param AttributeSubjectInterface|null $subject
     */
    public function setSubject(?AttributeSubjectInterface $subject): void;

    /**
     * @return AttributeInterface|null
     */
    public function getAttribute(): ?AttributeInterface;

    /**
     * @param AttributeInterface|null $attribute
     */
    public function setAttribute(?AttributeInterface $attribute): void;

    /**
     * @return mixed
     */
    public function getValue();

    /**
     * @param mixed $value
     */
    public function setValue($value): void;

    /**
     * @return string|null
     */
    public function getCode(): ?string;

    /**
     * @return string|null
     */
    public function getName(): ?string;

    /**
     * @return string|null
     */
    public function getType(): ?string;

    /**
     * @return string|null
     *
     * @throws \InvalidArgumentException
     */
    public function getLocaleCode(): ?string;

    /**
     * @param string|null $localeCode
     */
    public function setLocaleCode(?string $localeCode): void;
}
