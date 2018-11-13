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

namespace Sylius\Component\Mailer\Model;

interface EmailInterface
{
    public function getCode(): ?string;

    public function setCode(string $code): void;

    public function isEnabled(): bool;

    public function setEnabled(bool $enabled): void;

    public function enable(): void;

    public function disable(): void;

    /**
     * @return string
     */
    public function getSubject(): ?string;

    public function setSubject(string $subject): void;

    /**
     * @return string
     */
    public function getContent(): ?string;

    public function setContent(string $content);

    public function getTemplate(): ?string;

    public function setTemplate(string $template): void;

    public function getSenderName(): ?string;

    public function setSenderName(string $senderName): void;

    public function getSenderAddress(): ?string;

    public function setSenderAddress(string $senderAddress): void;
}
