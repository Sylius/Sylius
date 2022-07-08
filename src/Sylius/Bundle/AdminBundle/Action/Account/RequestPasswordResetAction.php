<?php

/*
 *  This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\AdminBundle\Action\Account;

use Sylius\Bundle\AdminBundle\Form\Model\PasswordResetRequest;
use Sylius\Bundle\AdminBundle\Form\RequestPasswordResetType;
use Sylius\Bundle\CoreBundle\Message\Admin\Account\RequestResetPasswordEmail;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\RouterInterface;

final class RequestPasswordResetAction
{
    public function __construct(
        private FormFactoryInterface $formFactory,
        private MessageBusInterface $messageBus,
        private FlashBagInterface $flashBag,
        private RouterInterface $router
    ) {
    }

    public function __invoke(Request $request): RedirectResponse
    {
        $requestPasswordReset = new PasswordResetRequest();
        $form = $this->formFactory->create(RequestPasswordResetType::class, $requestPasswordReset);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var PasswordResetRequest $formData */
            $formData = $form->getData();
            $requestPasswordResetMessage = new RequestResetPasswordEmail(
                $formData->getEmail()
            );

            $this->messageBus->dispatch($requestPasswordResetMessage);
        }

        $this->flashBag->set('success', 'sylius.admin.request_reset_password.success');

        return new RedirectResponse($this->router->generate('sylius_admin_login'));
    }
}
