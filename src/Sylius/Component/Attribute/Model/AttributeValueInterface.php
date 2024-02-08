<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
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

    public function getSubject(): ?AttributeSubjectInterface;

    public function setSubject(?AttributeSubjectInterface $subject): void;

    public function getAttribute(): ?AttributeInterface;

    public function setAttribute(?AttributeInterface $attribute): void;

    public function getValue();

    public function setValue($value): void;

    public function getCode(): ?string;

    public function getName(): ?string;

    public function getType(): ?string;

    /**
     * @throws \InvalidArgumentException
     */
    public function getLocaleCode(): ?string;

    public function setLocaleCode(?string $localeCode): void;
}
