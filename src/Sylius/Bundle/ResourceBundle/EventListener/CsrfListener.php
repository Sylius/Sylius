<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ResourceBundle\EventListener;

use Symfony\Component\Form\Extension\Csrf\CsrfProvider\CsrfProviderInterface;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * @author Geoffrey Brier <geoffrey.brier@gmail.com>
 */
class CsrfListener
{
    /** @var CsrfProviderInterface */
    protected $csrfProvider;

    /** @var string */
    protected $intention;

    /**
     * @param CsrfProviderInterface $csrfProvider
     * @param string                $intention
     */
    public function __construct(CsrfProviderInterface $csrfProvider, $intention)
    {
        $this->csrfProvider = $csrfProvider;
        $this->intention = $intention;
    }

    /**
     * {@inheritdoc}
     */
    public function onKernelController(FilterControllerEvent $event)
    {
        $request = $event->getRequest();

        if (!$request->isMethod('DELETE') || $event->getRequestType() !== HttpKernelInterface::MASTER_REQUEST) {
            return;
        }

        if (!$this->csrfProvider->isCsrfTokenValid($this->intention, $request->request->get('_csrf_token', ''))) {
            throw new AccessDeniedException();
        }
    }
}
