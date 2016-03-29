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
use Sylius\Component\Affiliate\Model\RewardInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
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
        /**
         * @var AffiliateInterface $affiliate
         */
        $affiliate = $this->getAffiliate();

        $view = $this
            ->view()
            ->setTemplate('SyliusWebBundle:Frontend/Account:Affiliate/index.html.twig')
            ->setData(array(
                'affiliate'          => $affiliate,
                'query_parameter'    => $this->getParameter('sylius_affiliate.referral.query_parameter'),
                'referrals_count'    => $affiliate->getReferrals()->count(),
                'rewards_count'      => $affiliate->getRewards()->count(),
            ))
        ;

        return $this->handleView($view);
    }

    /**
     * Create affiliation for the current user.
     *
     * @return Response
     */
    public function createAction(Request $request)
    {
        if (null !== $this->getAffiliate()) {
            return new RedirectResponse($this->generateUrl('sylius_account_affiliate_index'));
        }

        /** @var $affiliate AffiliateInterface */
        $affiliate  = $this->getAffiliateRepository()->createNew();
        /** @var $customer CustomerInterface */
        $customer   = $this->getCustomer();
        $form       = $this->getSignupForm();

        if ($form->handleRequest($request)->isValid()) {

            $settings = $this->get('sylius.settings.manager')->loadSettings('sylius_affiliate');

            if ($settings->get('enabled_multi_level') && $referrer = $customer->getReferrer()) {
                $affiliate->setReferrer($referrer);
            }

            $customer->setAffiliate($affiliate);

            $this->get('event_dispatcher')->dispatch('sylius.affiliate.pre_create', new GenericEvent($affiliate));

            $manager = $this->getCustomerManager();
            $manager->persist($customer);
            $manager->flush();

            $this->addFlash('success', 'sylius.account.affiliate.signup.success');

            return $this->redirectToIndex();
        }

        $view = $this
            ->view()
            ->setTemplate('SyliusWebBundle:Frontend/Account:Affiliate/create.html.twig')
            ->setData(array(
                'customer' => $this->getCustomer(),
                'form'     => $form->createView()
            ))
        ;

        return $this->handleView($view);
    }

    /**
     * Get history of rewards of the current customer.
     *
     * @return Response
     *
     * @throws NotFoundHttpException
     */
    public function historyAction()
    {
        $affiliate = $this->getAffiliate();
        $earning = $affiliate->getRewards()->matching(
            Criteria::create()
                ->where(Criteria::expr()->eq('type', RewardInterface::TYPE_EARNING))
                ->orderBy(array(
                    'updatedAt' => 'desc',
                ))
        );
        $payout = $affiliate->getRewards()->matching(
            Criteria::create()
                ->where(Criteria::expr()->eq('type', RewardInterface::TYPE_PAYOUT))
                ->orderBy(array(
                    'updatedAt' => 'desc',
                ))
        );

        $view = $this
            ->view()
            ->setTemplate('SyliusWebBundle:Frontend/Account:Affiliate/history.html.twig')
            ->setData(array(
                'rewards' => array(
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
                'referrals' => $this->getAffiliate()->getReferrals()
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
            $affiliate = $this->getAffiliate();

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
     * @return AffiliateInterface
     */
    private function getAffiliate()
    {
        return $this->get('sylius.context.customer')->getCustomer()->getAffiliate();
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

    /**
     * @return CustomerInterface
     */
    protected function getCustomer()
    {
        return $this->get('sylius.context.customer')->getCustomer();
    }

    /**
     * @return ObjectManager
     */
    private function getCustomerManager()
    {
        return $this->get('sylius.manager.customer');
    }

    protected function redirectToIndex()
    {
        return $this->redirect($this->generateUrl('sylius_account_affiliate_index'));
    }

    private function getSignupForm()
    {
        return $this->get('form.factory')->create('sylius_affiliate_signup');
    }
}
