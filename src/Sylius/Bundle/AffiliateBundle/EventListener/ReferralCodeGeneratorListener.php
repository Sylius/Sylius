<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\AffiliateBundle\EventListener;

use Doctrine\ORM\EntityManagerInterface;
use Sylius\Component\Affiliate\Model\AffiliateInterface;
use Sylius\Component\Resource\Exception\UnexpectedTypeException;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

class ReferralCodeGeneratorListener
{
    /**
     * @var RepositoryInterface
     */
    protected $repository;

    /**
     * @var EntityManagerInterface
     */
    protected $manager;

    public function __construct(RepositoryInterface $repository, EntityManagerInterface $manager)
    {
        $this->repository = $repository;
        $this->manager = $manager;
    }

    public function generateReferralCode(GenericEvent $event)
    {
        $affiliate = $event->getSubject();

        if (!$affiliate instanceof AffiliateInterface) {
            throw new UnexpectedTypeException($affiliate, 'Sylius\Component\Affiliate\Model\AffiliateInterface');
        }

        $affiliate->setReferralCode($this->generateUniqueCode());

        $this->manager->persist($affiliate);
        $this->manager->flush($affiliate);
    }

    /**
     * @return string
     */
    protected function generateUniqueCode()
    {
        do {
            $referrerCode = sha1(uniqid('sylius_affiliate').microtime(true));
        } while ($this->isUsedCode($referrerCode));

        return $referrerCode;
    }

    /**
     * @param string $referrerCode
     *
     * @return bool
     */
    protected function isUsedCode($referrerCode)
    {
        return null !== $this->repository->findOneBy(array('referralCode' => $referrerCode));
    }
}
