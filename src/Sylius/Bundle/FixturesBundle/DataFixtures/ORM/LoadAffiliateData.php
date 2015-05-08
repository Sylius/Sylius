<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\FixturesBundle\DataFixtures\ORM;

use Doctrine\Common\Persistence\ObjectManager;
use Sylius\Bundle\FixturesBundle\DataFixtures\DataFixture;
use Sylius\Component\Affiliate\Model\AffiliateInterface;
use Sylius\Component\Affiliate\Model\TransactionInterface;
use Sylius\Component\Core\Model\UserInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

/**
 * Affiliate fixtures.
 *
 * @author Joseph Bielawski <stloyd@gmail.com>
 */
class LoadAffiliateData extends DataFixture
{
    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager)
    {
        /** @var $user UserInterface */
        $user = $this->getReference('Sylius.User-Administrator');

        /** @var $affiliate AffiliateInterface */
        $affiliate = $this->getAffiliateRepository()->createNew();

        for ($i = 1; $i <= 50; $i++) {
            $transaction = $this->createTransaction(
                $this->faker->dateTimeBetween('1 month ago', 'now'),
                $i % 15 === 0 ? TransactionInterface::TYPE_PAYOUT : TransactionInterface::TYPE_EARNING,
                $i % 15 === 0 ? -rand(1000, 2000) : rand(100, 500)
            );

            $affiliate->addTransaction($transaction);

            $manager->persist($transaction);
        }

        for ($i = 2; $i <= 200; $i += 2) {
            $affiliate->addReferral($this->getReference('Sylius.User-'.$i));
        }

        $user->setAffiliate($affiliate);

        $this->get('event_dispatcher')->dispatch('sylius.affiliate.pre_create', new GenericEvent($affiliate));

        $manager->persist($user);
        $manager->persist($affiliate);
        $manager->flush();
    }

    /**
     * @param \DateTime $date
     * @param int       $type
     * @param int       $amount
     *
     * @return TransactionInterface
     */
    protected function createTransaction(\DateTime $date, $type, $amount)
    {
        /** @var $transaction TransactionInterface */
        $transaction = $this->getTransactionRepository()->createNew();
        $transaction->setType($type);
        $transaction->setAmount($amount);
        $transaction->setCurrency('EUR');
        $transaction->setCreatedAt($date);

        return $transaction;
    }

    /**
     * {@inheritdoc}
     */
    public function getOrder()
    {
        return 2;
    }
}
