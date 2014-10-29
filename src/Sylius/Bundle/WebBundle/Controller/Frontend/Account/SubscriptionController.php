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

use Sylius\Bundle\ResourceBundle\Controller\ResourceController;
use Sylius\Component\Core\Model\SubscriptionInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class SubscriptionController extends ResourceController
{
    /**
     * {@inheritdoc}
     */
    public function indexAction(Request $request)
    {
        $config = $this->getConfiguration();

        $subscriptions = $this->getRepository()->findByUser($this->getUser());

        $view = $this
            ->view()
            ->setTemplate($config->getTemplate('index.html'))
            ->setTemplateVar($config->getPluralResourceName())
            ->setData($subscriptions)
        ;

        return $this->handleView($view);
    }

    /**
     * {@inheritdoc}
     */
    public function findOr404(Request $request, array $criteria = array())
    {
        if ($resource = parent::findOr404($request, $criteria)) {
            $this->accessSubscriptionOr403($resource);
        }

        return $resource;
    }

    /**
     * Accesses subscription or throws 403
     *
     * @param  SubscriptionInterface $subscription
     * @throws AccessDeniedException
     */
    private function accessSubscriptionOr403(SubscriptionInterface $subscription)
    {
        if (!$this->getUser()->hasSubscription($subscription)) {
            throw new AccessDeniedException();
        }
    }
}
