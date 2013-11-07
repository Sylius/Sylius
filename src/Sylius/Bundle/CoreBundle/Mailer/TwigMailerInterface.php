<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Mailer;

/**
 * TwigMailerInterface
 *
 * @author Daniel Richter <nexyz9@gmail.com>
 */
interface TwigMailerInterface
{
    /**
     * Render a template and send result as email
     *
     * @param string       $templateName
     * @param array        $context
     * @param string|array $fromEmail
     * @param string       $toEmail
     */
    public function sendEmail($templateName, $context, $fromEmail, $toEmail);
}
