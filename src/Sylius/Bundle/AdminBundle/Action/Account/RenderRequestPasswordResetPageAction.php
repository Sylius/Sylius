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

use Sylius\Bundle\AdminBundle\Form\RequestPasswordResetType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

final class RenderRequestPasswordResetPageAction
{
    public function __construct(
        private Environment $twig,
        private FormFactoryInterface $formFactory,
    ) {
    }

    public function __invoke(): Response
    {
        $form = $this->formFactory->create(RequestPasswordResetType::class);

        return new Response(
            $this->twig->render('@SyliusAdmin/Security/requestPasswordReset.html.twig', [
                'form' => $form->createView(),
            ]),
        );
    }
}
