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

use Doctrine\ORM\EntityManagerInterface;
use Sylius\Component\Affiliate\Model\AffiliateInterface;
use Sylius\Component\Affiliate\Model\InvitationInterface;
use Sylius\Component\Mailer\Sender\SenderInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

/**
 * Default invitation generator.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class InvitationGenerator implements InvitationGeneratorInterface
{
    /**
     * @var RepositoryInterface
     */
    protected $repository;

    /**
     * @var EntityManagerInterface
     */
    protected $manager;

    /**
     * @var SenderInterface
     */
    protected $emailSender;

    public function __construct(RepositoryInterface $repository, EntityManagerInterface $manager, SenderInterface $emailSender)
    {
        $this->repository = $repository;
        $this->manager = $manager;
        $this->emailSender = $emailSender;
    }

    /**
     * {@inheritdoc}
     */
    public function generate(AffiliateInterface $affiliate, Instruction $instruction)
    {
        foreach ($instruction->getEmails() as $email) {
            /** @var $invitation InvitationInterface */
            $invitation = $this->repository->createNew();
            $invitation->setAffiliate($affiliate);
            $invitation->setEmail($email);
            $invitation->setHash($this->generateUniqueHash($email, $instruction->getReferrerCode()));

            $this->emailSender->send('affiliate_invitation', array($email), array('affiliate' => $affiliate, 'invitation' => $invitation));

            $this->manager->persist($invitation);
        }

        $this->manager->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function generateUniqueHash($email, $referrerCode)
    {
        do {
            $hash = sha1($email.$referrerCode.microtime(true));
        } while ($this->isUsedHash($hash));

        return $hash;
    }

    /**
     * @param string $hash
     *
     * @return bool
     */
    protected function isUsedHash($hash)
    {
        return null !== $this->repository->findOneBy(array('code' => $hash));
    }
}
