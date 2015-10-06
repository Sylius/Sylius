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

/**
 * Invitation generate instruction.
 *
 * @author Joseph Bielawski <stloyd@gmail.com>
 */
class Instruction
{
    protected $emails = array();
    protected $referrerCode;

    public function getEmails()
    {
        return $this->emails;
    }

    public function addEmail($email)
    {
        $this->emails[] = $email;

        return $this;
    }

    public function getReferrerCode()
    {
        return $this->referrerCode;
    }

    public function setReferrerCode($referrerCode)
    {
        $this->referrerCode = $referrerCode;

        return $this;
    }
}
