<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\UserBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Sylius\Bundle\ResourceBundle\Controller\ResourceController;
use Sylius\Bundle\UserBundle\Form\Model\ChangePassword;
use Sylius\Bundle\UserBundle\Form\Type\UserChangePasswordType;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
class UserController extends ResourceController
{
    public function updateProfileAction(Request $request)
    {
        $resource = $this->getUser();
        $form     = $this->getForm($resource);

        if (in_array($request->getMethod(), array('POST', 'PUT', 'PATCH')) && $form->submit($request, !$request->isMethod('PATCH'))->isValid()) {
            $this->domainManager->update($resource);

            if ($this->config->isApiRequest()) {
                return $this->handleView($this->view($resource, 204));
            }

            return $this->redirectHandler->redirectTo($resource);
        }

        if ($this->config->isApiRequest()) {
            return $this->handleView($this->view($form, 400));
        }

        $view = $this
            ->view()
            ->setTemplate($this->config->getTemplate('updateProfile.html'))
            ->setData(array(
                $this->config->getResourceName() => $resource,
                'form'                           => $form->createView()
            ))
        ;

        return $this->handleView($view);
    }

    public function changePasswordAction(Request $request)
    {
        $user = $this->getUser();
        $changePassword = new ChangePassword();
        $form = $this->createForm(new UserChangePasswordType(), $changePassword);

        if (in_array($request->getMethod(), array('POST', 'PUT', 'PATCH')) && $form->submit($request, !$request->isMethod('PATCH'))->isValid()) {
            $encoderFactory = $this->get('security.encoder_factory');

            $encoder = $encoderFactory->getEncoder($user);
            $validPassword = $encoder->isPasswordValid(
                $user->getPassword(),
                $changePassword->getCurrentPassword(),      
                $user->getSalt()
            );

            if ($validPassword) {
                $user->setPlainPassword($changePassword->getNewPassword());
                $this->domainManager->update($user);
                return $this->render(
                    'SyliusWebBundle:Frontend/Account:changePassword.html.twig',
                    array(
                        'form'  => $form->createView(),
                    )
                );
            }

            $request->getSession()->getFlashBag()->add(
                'error',
                'sylius.user.form.password.invalid'
            );
        }

        if ($this->config->isApiRequest()) {
            return $this->handleView($this->view($form, 400));
        }

        return $this->render(
            'SyliusWebBundle:Frontend/Account:changePassword.html.twig',
            array(
                'form'  => $form->createView(),
            )
        );
    }
}
