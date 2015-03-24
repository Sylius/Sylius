<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Affiliate\Model;

use Sylius\Component\Resource\Model\TimestampableInterface;

/**
 * @author Joseph Bielawski <stloyd@gmail.com>
 */
interface InvitationInterface extends AffiliateAwareInterface, TimestampableInterface
{
    const INVITATION_SENT = 0;
    const INVITATION_USED = 1;

    /**
     * Get ID.
     *
     * @return int
     */
    public function getId();

    /**
     * @return string
     */
    public function getEmail();

    /**
     * @param string $email
     *
     * @return self
     */
    public function setEmail($email);

    /**
     * @return string
     */
    public function getHash();

    /**
     * @param string $hash
     *
     * @return self
     */
    public function setHash($hash);

    /**
     * @return bool
     */
    public function isUsed();

    /**
     * @return int
     */
    public function getStatus();

    /**
     * @param int $status
     *
     * @return self
     */
    public function setStatus($status);

    /**
     * @return \DateTime
     */
    public function getExpireAt();

    /**
     * @param \DateTime $expireAt
     *
     * @return self
     */
    public function setExpireAt(\DateTime $expireAt = null);
}
