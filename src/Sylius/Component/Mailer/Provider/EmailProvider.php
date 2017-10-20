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

namespace Sylius\Component\Mailer\Provider;

use Sylius\Component\Mailer\Factory\EmailFactoryInterface;
use Sylius\Component\Mailer\Model\EmailInterface;
use Webmozart\Assert\Assert;

final class EmailProvider implements EmailProviderInterface
{
    /**
     * @var EmailFactoryInterface
     */
    private $emailFactory;

    /**
     * @var array
     */
    private $configuration;

    /**
     * @param EmailFactoryInterface $emailFactory
     * @param array $configuration
     */
    public function __construct(
        EmailFactoryInterface $emailFactory,
        array $configuration
    ) {
        $this->emailFactory = $emailFactory;
        $this->configuration = $configuration;
    }

    /**
     * {@inheritdoc}
     */
    public function getEmail(string $code): EmailInterface
    {
        return $this->getEmailFromConfiguration($code);
    }

    /**
     * @param string $code
     *
     * @return EmailInterface
     */
    private function getEmailFromConfiguration(string $code): EmailInterface
    {
        Assert::keyExists($this->configuration, $code, sprintf('Email with code "%s" does not exist!', $code));

        /** @var EmailInterface $email */
        $email = $this->emailFactory->createNew();
        $configuration = $this->configuration[$code];

        $email->setCode($code);
        $email->setSubject($configuration['subject']);
        $email->setTemplate($configuration['template']);

        if (isset($configuration['enabled']) && false === $configuration['enabled']) {
            $email->setEnabled(false);
        }
        if (isset($configuration['sender']['name'])) {
            $email->setSenderName($configuration['sender']['name']);
        }
        if (isset($configuration['sender']['address'])) {
            $email->setSenderAddress($configuration['sender']['address']);
        }

        return $email;
    }
}
