<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\WebBundle\Controller\Frontend\Account;

use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Persistence\ObjectManager;
use FOS\RestBundle\Controller\FOSRestController;
use Sylius\Component\Affiliate\Generator\Instruction;
use Sylius\Component\Affiliate\Generator\InvitationGeneratorInterface;
use Sylius\Component\Affiliate\Model\AffiliateInterface;
use Sylius\Component\Affiliate\Model\TransactionInterface;
use Sylius\Component\Core\Model\UserInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Account affiliate controller.
 *
 * @author Joseph Bielawski <stloyd@gmail.com>
 */
class AffiliateController extends FOSRestController
{
    /**
     * List affiliates of the current user.
     *
     * @return Response
     */
    public function indexAction()
    {
        $affiliate = $this->getUser()->getAffiliate();

        $view = $this
            ->view()
            ->setTemplate('SyliusWebBundle:Frontend/Account:Affiliate/index.html.twig')
            ->setData(array(
                'affiliate' => $affiliate,
                'referrals_count' => $affiliate->getReferrals()->count(),
                'transactions_count' => $affiliate->getTransactions()->count(),
            ))
        ;

        return $this->handleView($view);
    }

    /**
     * Create affiliation for the current user.
     *
     * @return Response
     */
    public function createAction()
    {
        /** @var $user UserInterface */
        $user = $this->getUser();
        if (null !== $user->getAffiliate()) {
            return new RedirectResponse($this->generateUrl('sylius_account_affiliate_index'));
        }

        /** @var $affiliate AffiliateInterface */
        $affiliate = $this->getAffiliateRepository()->createNew();
        $settings  = $this->get('sylius.settings.affiliate');
        if ($settings->get('enable_multi_level') && $referrer = $user->getReferrer()) {
            $affiliate->setReferrer($referrer);
        }

        $user->setAffiliate($affiliate);

        $manager = $this->getUserManager();
        $manager->persist($affiliate);
        $manager->persist($user);
        $manager->flush();

        return new RedirectResponse($this->generateUrl('sylius_account_affiliate_index'));
    }

    /**
     * Get history of transactions of the current user.
     *
     * @return Response
     *
     * @throws NotFoundHttpException
     */
    public function historyAction()
    {
        $affiliate = $this->getUser()->getAffiliate();
        $earning = $affiliate->getTransactions()->matching(
            Criteria::create()
                ->where(Criteria::expr()->eq('type', TransactionInterface::TYPE_EARNING))
                ->orderBy(array(
                    'updatedAt' => 'desc',
                ))
        );
        $payout = $affiliate->getTransactions()->matching(
            Criteria::create()
                ->where(Criteria::expr()->eq('type', TransactionInterface::TYPE_PAYOUT))
                ->orderBy(array(
                    'updatedAt' => 'desc',
                ))
        );

        $view = $this
            ->view()
            ->setTemplate('SyliusWebBundle:Frontend/Account:Affiliate/history.html.twig')
            ->setData(array(
                'transactions' => array(
                    'earning' => $earning,
                    'payout' => $payout
                ),
            ))
        ;

        return $this->handleView($view);
    }

    /**
     * Get referrals of the current user.
     *
     * @return Response
     *
     * @throws NotFoundHttpException
     */
    public function referralsAction()
    {
        $view = $this
            ->view()
            ->setTemplate('SyliusWebBundle:Frontend/Account:Affiliate/referrals.html.twig')
            ->setData(array(
                'referrals' => $this->getUser()->getAffiliate()->getReferrals()
            ))
        ;

        return $this->handleView($view);
    }

    /**
     * Send invitations.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function invitationsAction(Request $request)
    {
        $form = $this->get('form.factory')->create('sylius_affiliate_invitations');
        if ($form->handleRequest($request)->isValid()) {
            $affiliate = $this->getUser()->getAffiliate();

            $instruction = new Instruction();
            $instruction->setReferrerCode($affiliate->getReferralCode());

            $repository = $this->getInvitationRepository();
            foreach (explode("\n", $form->getData()) as $email) {
                if (!$repository->findOneBy(array('email' => trim($email)))) {
                    $instruction->addEmail($email);
                }
            }

            $this->getInvitationGenerator()->generate($affiliate, $instruction);

            return new RedirectResponse($this->generateUrl('sylius_account_affiliate_index'));
        }

        $view = $this
            ->view()
            ->setTemplate('SyliusWebBundle:Frontend/Account:Affiliate/invitations.html.twig')
            ->setData(array(
                'form' => $form
            ))
        ;

        return $this->handleView($view);
    }

    /**
     * @return RepositoryInterface
     */
    private function getAffiliateRepository()
    {
        return $this->get('sylius.repository.affiliate');
    }

    /**
     * @return RepositoryInterface
     */
    private function getInvitationRepository()
    {
        return $this->get('sylius.repository.invitation');
    }

    /**
     * @return ObjectManager
     */
    private function getUserManager()
    {
        return $this->get('sylius.manager.user');
    }

    /**
     * @return InvitationGeneratorInterface
     */
    private function getInvitationGenerator()
    {
        return $this->get('sylius.generator.invitation_generator');
    }
}
