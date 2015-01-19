<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Mailer\Provider;

use Sylius\Component\Mailer\Model\EmailInterface;
use Sylius\Component\Resource\Factory\ResourceFactoryInterface;
use Sylius\Component\Resource\Repository\ResourceRepositoryInterface;

/**
 * Default email provider implementation.
 *
 * Looks in database and then configuration array.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 * @author Jérémy Leherpeur <jeremy@leherpeur.net>
 */
class EmailProvider implements EmailProviderInterface
{
    /**
     * Repository for email model.
     *
     * @var ResourceRepositoryInterface
     */
    protected $emailRepository;

    /**
     * @var ResourceFactoryInterface
     */
    protected $emailFactory;

    /**
     * Configuration.
     *
     * @var array
     */
    protected $configuration;

    /**
     * @param ResourceRepositoryInterface $emailRepository
     * @param ResourceFactoryInterface    $emailFactory
     * @param array                       $configuration
     */
    public function __construct(ResourceRepositoryInterface $emailRepository, ResourceFactoryInterface $emailFactory, array $configuration)
    {
        $this->emailRepository = $emailRepository;
        $this->emailFactory = $emailFactory;
        $this->configuration = $configuration;
    }

    /**
     * {@inheritdoc}
     */
    public function getEmail($code)
    {
        $email = $this->emailRepository->findOneBy(array('code' => $code));

        if (null === $email) {
            $email = $this->getEmailFromConfiguration($code);
        }

        return $email;
    }

    /**
     * @param string $code
     *
     * @return EmailInterface
     */
    protected function getEmailFromConfiguration($code)
    {
        if (!isset($this->configuration[$code])) {
            throw new \InvalidArgumentException(sprintf('Email with code "%s" does not exist!', $code));
        }

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
