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
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Sylius\Bundle\CoreBundle\Model\SubscriptionInterface;
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

        $subscriptions = $this
            ->getRepository()
            ->findBy(array('user' => $this->getUser()), array('scheduledDate' => 'desc'));

        $view = $this
            ->view()
            ->setTemplate($config->getTemplate('index.html'))
            ->setTemplateVar($config->getPluralResourceName())
            ->setData($subscriptions)
        ;

        return $this->handleView($view);
    }

    /**
     * Accesses subscription or throws 403
     *
     * @param SubscriptionInterface $subscription
     * @throws AccessDeniedException
     */
    private function accessSubscriptionOr403(SubscriptionInterface $subscription)
    {
        if (false === $this->get('security.context')->isGranted('ROLE_SYLIUS_ADMIN') &&
            $this->getUser()->getId() !== $subscription->getUser()->getId()) {
            throw new AccessDeniedException();
        }
    }

    public function findOr404(array $criteria = null)
    {
        if ($resource = parent::findOr404($criteria)) {
            $this->accessSubscriptionOr403($resource);
        }

        return $resource;
    }
}