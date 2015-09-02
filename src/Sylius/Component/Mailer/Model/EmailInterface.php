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
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 * @author Manuel Gonzalez <mgonyan@gmail.com>
 */
interface EmailInterface extends TimestampableInterface, EmailTranslationInterface
{
    /**
     * @return string
     */
    public function getCode();

    /**
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
