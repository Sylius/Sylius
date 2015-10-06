<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\AffiliateBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Sylius\Bundle\ResourceBundle\Controller\ResourceController;
use Sylius\Component\Resource\Event\ResourceEvent;
use Sylius\Component\Affiliate\Model\AffiliateInterface;

class AffiliateController extends ResourceController
{
    /**
     * @param Request $request
     * @return RedirectResponse|Response
     * @throws HttpException
     */
    public function pauseAction(Request $request)
    {
        return $this->status($request, AffiliateInterface::AFFILIATE_PAUSED, 'pause');
    }

    /**
     * @param Request $request
     * @return RedirectResponse|Response
     * @throws HttpException
     */
    public function enableAction(Request $request)
    {
        return $this->status($request, AffiliateInterface::AFFILIATE_ENABLED, 'enable');
    }

    /**
     * @param Request $request
     * @return RedirectResponse|Response
     * @throws HttpException
     */
    public function disableAction(Request $request)
    {
        return $this->status($request, AffiliateInterface::AFFILIATE_DISABLED, 'disable');
    }

    /**
     * @param Request $request
     * @param $status
     * @param $flash
     * @return RedirectResponse|Response
     */
    protected function status(Request $request, $status, $flash)
    {
        $this->isGrantedOr403('update');

        $affiliate = $this->findOr404($request);
        $affiliate->setStatus($status);

        $this->domainManager->update($affiliate, $flash);

        if ($this->config->isApiRequest()) {
            if ($affiliate instanceof ResourceEvent) {
                throw new HttpException($affiliate->getErrorCode(), $affiliate->getMessage());
            }

            return $this->handleView($this->view($affiliate, 204));
        }

        return $this->redirectHandler->redirectToIndex();
    }
}
