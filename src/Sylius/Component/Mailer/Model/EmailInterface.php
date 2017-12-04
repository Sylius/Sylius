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
    /**
     * @return string|null
     */
    public function getCode(): ?string;

    /**
     * @param string $code
     */
    public function setCode(string $code): void;

    /**
     * @return bool
     */
    public function isEnabled(): bool;

    /**
     * @param bool $enabled
     */
    public function setEnabled(bool $enabled): void;

    public function enable(): void;

    public function disable(): void;

    /**
     * @return string
     */
    public function getSubject(): ?string;

    /**
     * @param string $subject
     */
    public function setSubject(string $subject): void;

    /**
     * @return string
     */
    public function getContent(): ?string;

    /**
     * @param string $content
     */
    public function setContent(string $content);

    /**
     * @return string|null
     */
    public function getTemplate(): ?string;

    /**
     * @param string $template
     */
    public function setTemplate(string $template): void;

    /**
     * @return string|null
     */
    public function getSenderName(): ?string;

    /**
     * @param string $senderName
     */
    public function setSenderName(string $senderName): void;

    /**
     * @return string|null
     */
    public function getSenderAddress(): ?string;

    /**
     * @param string $senderAddress
     */
    public function setSenderAddress(string $senderAddress): void;
}
