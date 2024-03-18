<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\AdminBundle\Action\Account;

use Sylius\Bundle\AdminBundle\Form\Model\PasswordResetRequest;
use Sylius\Bundle\AdminBundle\Form\RequestPasswordResetType;
use Sylius\Bundle\CoreBundle\Message\Admin\Account\RequestResetPasswordEmail;
use Sylius\Bundle\CoreBundle\Provider\FlashBagProvider;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\RouterInterface;
use Twig\Environment;

final class RequestPasswordResetAction
{
    public function __construct(
        private FormFactoryInterface $formFactory,
        private MessageBusInterface $messageBus,
        private FlashBagInterface|RequestStack $requestStackOrFlashBag,
        private RouterInterface $router,
        private Environment $twig,
    ) {
        if ($this->requestStackOrFlashBag instanceof FlashBagInterface) {
            trigger_deprecation(
                'sylius/admin-bundle',
                '1.12',
                'Passing an instance of %s as constructor argument for %s is deprecated and will be removed in Sylius 2.0. Pass an instance of %s instead.',
                FlashBagInterface::class,
                self::class,
                RequestStack::class,
            );
        }
    }

    public function __invoke(Request $request): Response
    {
        $form = $this->formFactory->create(RequestPasswordResetType::class);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var PasswordResetRequest $formData */
            $formData = $form->getData();
            $requestPasswordResetMessage = new RequestResetPasswordEmail(
                $formData->getEmail(),
            );

            $this->messageBus->dispatch($requestPasswordResetMessage);

            FlashBagProvider
                ::getFlashBag($this->requestStackOrFlashBag)
                ->add('success', 'sylius.admin.request_reset_password.success')
            ;

            $options = $request->attributes->get('_sylius', []);
            $redirectRoute = $options['redirect'] ?? 'sylius_admin_login';

            if (is_array($redirectRoute)) {
                return new RedirectResponse($this->router->generate(
                    $redirectRoute['route'] ?? 'sylius_admin_login',
                    $redirectRoute['params'] ?? [],
                ));
            }

            return new RedirectResponse($this->router->generate($redirectRoute));
        }

        return new Response(
            $this->twig->render('@SyliusAdmin/Security/requestPasswordReset.html.twig', [
                'form' => $form->createView(),
            ]),
        );
    }
}
