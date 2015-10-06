<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Affiliate\Generator;

use Sylius\Component\Affiliate\Model\AffiliateInterface;

/**
 * Coupon generator interface.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
interface InvitationGeneratorInterface
{
    /**
     * Generate invitations for the affiliate based on the instruction.
     *
     * @param AffiliateInterface $affiliate
     * @param Instruction        $instruction
     */
    public function generate(AffiliateInterface $affiliate, Instruction $instruction);

    /**
     * Generate unique hash.
     *
     * @param string $email
     * @param string $referrerCode
     *
     * @return string
     */
    public function generateUniqueHash($email, $referrerCode);
}
