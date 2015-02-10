<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Mailer\Model;

use Sylius\Component\Resource\Model\TimestampableInterface;

/**
 * Email interface.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
interface EmailInterface extends TimestampableInterface
{
    /**
     * Get code.
     *
     * @return string
     */
    public function getCode();

    /**
     * Set code.
     *
     * @param string $code
     */
    public function setCode($code);

    /**
     * Is activated?
     *
     * @return Boolean
     */
    public function isEnabled();

    /**
     * Set activation status.
     *
     * @param Boolean $enabled
     */
    public function setEnabled($enabled);

    /**
     * @return string
     */
    public function getSubject();

    /**
     * @param string $subject
     *
     * @return string
     */
    public function setSubject($subject);

    /**
     * @return string
     */
    public function getContent();

    /**
     * @param string $content
     *
     * @return string
     */
    public function setContent($content);

    /**
     * @return string
     */
    public function getTemplate();

    /**
     * @param string $template
     */
    public function setTemplate($template);

    /**
     * @return string
     */
    public function getSenderName();

    /**
     * @param string $senderName
     *
     * @return string
     */
    public function setSenderName($senderName);

    /**
     * @return string
     */
    public function getSenderAddress();

    /**
     * @param string $senderAddress
     *
     * @return string
     */
    public function setSenderAddress($senderAddress);
}
