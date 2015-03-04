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

use Doctrine\Common\Persistence\ObjectManager;
use FOS\RestBundle\Controller\FOSRestController;
use Sylius\Component\Affiliate\Model\AffiliateInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
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
        $view = $this
            ->view()
            ->setTemplate('SyliusWebBundle:Frontend/Account:Affiliate/index.html.twig')
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
        $settings = $this->get('sylius.settings.affiliate');

        $affiliate = $this->getAffiliateRepository()->createNew();
        $affiliate->setProvisionType($settings->get('provision_type'));
        $affiliate->setProvisionAmount($settings->get('provision_amount'));

        $user = $this->getUser();
        $user->setAffiliate($affiliate);

        $manager = $this->getUserManager();
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
        $transactions = $this->getTransactionsRepository()->findBy(array('affiliate' => $this->getUser()->getAffiliate()), array('updatedAt' => 'desc'));

        $view = $this
            ->view()
            ->setTemplate('SyliusWebBundle:Frontend/Account:Affiliate/history.html.twig')
            ->setData(array(
                'transactions' => $transactions
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
    {die(var_dump($this->getUser()->getAffiliate()->getReferrals()->count()));
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
     * @return RepositoryInterface
     */
    private function getAffiliateRepository()
    {
        return $this->get('sylius.repository.affiliate');
    }

    /**
     * @return RepositoryInterface
     */
    private function getTransactionsRepository()
    {
        return $this->get('sylius.repository.transaction');
    }

    /**
     * @return ObjectManager
     */
    private function getUserManager()
    {
        return $this->get('sylius.manager.user');
    }
}
