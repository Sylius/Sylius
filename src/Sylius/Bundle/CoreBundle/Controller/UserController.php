<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Controller;

use Sylius\Bundle\ResourceBundle\Controller\ResourceController;
use Sylius\Component\Resource\Event\ResourceEvent;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use FOS\UserBundle\Doctrine\UserManager;

class UserController extends ResourceController
{
    /**
     * Render user filter form.
     */
    public function filterFormAction(Request $request)
    {
        return $this->render(
            'SyliusWebBundle:Backend/User:filterForm.html.twig',
            array(
                'form' => $this->get('form.factory')->createNamed(
                    'criteria',
                    'sylius_user_filter',
                    $request->query->get('criteria')
                )->createView()
            )
        );
    }

    /**
     * @param Request $request
     *
     * @return RedirectResponse|Response
     */
    public function createAction(Request $request)
    {
        /** @var UserManager $userManager */
        $userManager = $this->get('fos_user.user_manager');

        $user = $userManager->createUser();
        $form = $this->getForm($user);

        if ($request->isMethod('POST') && $form->submit($request)->isValid()) {
            $event = $this->dispatchEvent('pre_create', new ResourceEvent($user));

            if ($event->isStopped()) {
                if (null !== $this->flashHelper) {
                    $this->flashHelper->setFlash(
                        $event->getMessageType(),
                        $event->getMessage(),
                        $event->getMessageParameters()
                    );
                }

                return null;
            }

            $userManager->updateUser($user, true);

            if (null !== $this->flashHelper) {
                $this->flashHelper->setFlash('success', 'create');
            }

            $this->dispatchEvent('post_create', new ResourceEvent($user));

            if ($this->config->isApiRequest()) {
                return $this->handleView($this->view($user, 201));
            }

            if (null === $user) {
                return $this->redirectHandler->redirectToIndex();
            }

            return $this->redirectHandler->redirectTo($user);
        }

        if ($this->config->isApiRequest()) {
            return $this->handleView($this->view($form, 400));
        }

        $view = $this
            ->view()
            ->setTemplate($this->config->getTemplate('create.html'))
            ->setData(
                array(
                    $this->config->getResourceName() => $user,
                    'form' => $form->createView()
                )
            );

        return $this->handleView($view);
    }

    /**
     * @param Request $request
     *
     * @return RedirectResponse|Response
     */
    public function updateAction(Request $request)
    {
        $user = $this->findOr404($request);
        $form = $this->getForm($user);

        if (in_array($request->getMethod(), array('POST', 'PUT', 'PATCH')) && $form->submit(
                $request,
                !$request->isMethod('PATCH')
            )->isValid()
        ) {
            $event = $this->dispatchEvent('pre_update', new ResourceEvent($user));

            if ($event->isStopped()) {
                if (null !== $this->flashHelper) {
                    $this->flashHelper->setFlash(
                        $event->getMessageType(),
                        $event->getMessage(),
                        $event->getMessageParameters()
                    );
                }

                return null;
            }

            $this->get('fos_user.user_manager')->updateUser($user);

            if (null !== $this->flashHelper) {
                $this->flashHelper->setFlash('success', 'update');
            }

            $this->dispatchEvent('post_update', new ResourceEvent($user));

            if ($this->config->isApiRequest()) {
                return $this->handleView($this->view($user, 204));
            }

            return $this->redirectHandler->redirectTo($user);
        }

        if ($this->config->isApiRequest()) {
            return $this->handleView($this->view($form));
        }

        $view = $this
            ->view()
            ->setTemplate($this->config->getTemplate('update.html'))
            ->setData(
                array(
                    $this->config->getResourceName() => $user,
                    'form' => $form->createView()
                )
            );

        return $this->handleView($view);
    }

    private function getEventDispatcher()
    {
        return $this->container->get('event_dispatcher');
    }

    private function dispatchEvent($event, $resurce)
    {
        return $this->getEventDispatcher()->dispatch($event, new ResourceEvent($resurce));
    }
}
