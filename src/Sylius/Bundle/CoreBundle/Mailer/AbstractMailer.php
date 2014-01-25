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
 * Abstract mailer to simplify other mailer implementations
 *
 * @author Daniel Richter <nexyz9@gmail.com>
 */
abstract class AbstractMailer
{
    /**
     * @var TwigMailerInterface
     */
    protected $mailer;

    /**
     * @var array
     */
    protected $parameters;

    /**
     * Constructor
     *
     * @param TwigMailerInterface $mailer
     * @param array $parameters
     */
    public function __construct(TwigMailerInterface $mailer, array $parameters)
    {
        $this->mailer = $mailer;
        $this->parameters = $parameters;
    }

    /**
     * @param array $context
     * @param string $recipient
     */
    protected function sendEmail(array $context, $recipient)
    {
        $this->mailer->sendEmail(
            $this->parameters['template'],
            $context,
            $this->parameters['from_email'],
            $recipient
        );
    }
}
