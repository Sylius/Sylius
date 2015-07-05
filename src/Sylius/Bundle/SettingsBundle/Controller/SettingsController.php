<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\SettingsBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use Sylius\Bundle\SettingsBundle\Form\Factory\SettingsFormFactoryInterface;
use Sylius\Bundle\SettingsBundle\Manager\SettingsManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\Validator\Exception\ValidatorException;

/**
 * Settings controller.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class SettingsController extends FOSRestController
{
    /**
     * Edit configuration with given namespace.
     *
     * @param Request $request
     * @param string  $namespace
     *
     * @return Response
     */
    public function updateAction(Request $request, $namespace)
    {
        $manager = $this->getSettingsManager();
        $settings = $manager->loadSettings($namespace);
        $isApiRequest = $this->isApiRequest($request);

        $form = $this
            ->getSettingsFormFactory()
            ->create($namespace, $settings, $isApiRequest ? array('csrf_protection' => false) : array())
        ;

        if ($form->handleRequest($request)->isValid()) {
            $messageType = 'success';
            try {
                $manager->saveSettings($namespace, $form->getData());
                $message = $this->getTranslator()->trans('sylius.settings.update', array(), 'flashes');
            } catch (ValidatorException $exception) {
                $message = $this->getTranslator()->trans($exception->getMessage(), array(), 'validators');
                $messageType = 'error';
            }

            if ($isApiRequest) {
                return $this->handleView($this->view($settings, 204));
            }

            $request->getSession()->getBag('flashes')->add($messageType, $message);

            if ($request->headers->has('referer')) {
                return $this->redirect($request->headers->get('referer'));
            }
        }

        return $this->render($request->attributes->get('template', 'SyliusSettingsBundle:Settings:update.html.twig'), array(
            'settings' => $settings,
            'form'     => $form->createView(),
        ));
    }

    /**
     * Get settings manager.
     *
     * @return SettingsManagerInterface
     */
    protected function getSettingsManager()
    {
        return $this->get('sylius.settings.manager');
    }

    /**
     * Get settings form factory.
     *
     * @return SettingsFormFactoryInterface
     */
    protected function getSettingsFormFactory()
    {
        return $this->get('sylius.settings.form_factory');
    }

    /**
     * Get translator.
     *
     * @return TranslatorInterface
     */
    protected function getTranslator()
    {
        return $this->get('translator');
    }

    /**
     * Check that user can change given namespace.
     *
     * @param string $namespace
     *
     * @return bool
     */
    protected function isGrantedOr403($namespace)
    {
        if (!$this->container->has('sylius.authorization_checker')) {
            return true;
        }

        if (!$this->get('sylius.authorization_checker')->isGranted(sprintf('sylius.settings.%s', $namespace))) {
            throw new AccessDeniedException();
        }

        return false;
    }

    /**
     * @param Request $request
     * @return bool
     */
    private function isApiRequest(Request $request)
    {
        return 'html' !== $request->getRequestFormat();
    }
}
