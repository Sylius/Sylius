<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\AdminBundle\Action\Account;

use Sylius\Bundle\AdminBundle\Form\Model\PasswordReset;
use Sylius\Bundle\AdminBundle\Form\Type\ResetPasswordType;
use Sylius\Bundle\CoreBundle\Message\Admin\Account\ResetPassword;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\RouterInterface;

final class ResetPasswordAction
{
    public function __construct(
        private FormFactoryInterface $formFactory,
        private MessageBusInterface $messageBus,
        private FlashBagInterface $flashBag,
        private RouterInterface $router
    ) {
    }

    public function __invoke(Request $request, string $token): Response
    {
        $form = $this->formFactory->create(ResetPasswordType::class);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var PasswordReset $formData */
            $formData = $form->getData();
            $resetPasswordCommand = new ResetPassword(
                $token,
                $formData->getPassword(),
            );

            $this->messageBus->dispatch($resetPasswordCommand);
        }

        $this->flashBag->add('success', 'sylius.admin.reset_password.success');

        $attributes = $request->attributes->get('_sylius');
        $redirect = $attributes['redirect'] ?? 'sylius_admin_login';

        if (is_array($redirect)) {
            return new RedirectResponse(
                $redirect['route'] ?? 'sylius_admin_login',
                $redirect['params'] ?? [],
            );
        }

        return new RedirectResponse($this->router->generate($redirect));
    }
}
