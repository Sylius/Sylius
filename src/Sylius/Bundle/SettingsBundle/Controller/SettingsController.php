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
use Sylius\Bundle\SettingsBundle\Model\Settings;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\OptionsResolver\Exception\MissingOptionsException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\Validator\Exception\ValidatorException;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class SettingsController extends FOSRestController
{
    /**
     * Get a specific settings data.
     * This controller action only used for Rest API.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function showAction(Request $request)
    {
        $namespace = $request->get('namespace');

        $this->isGrantedOr403($namespace);

        try {
            $settings = $this->getSettingsManager()->loadSettings($namespace);
        } catch (MissingOptionsException $e) {
            // When a Settings is not persisted yet, it won't have any initial value in database,
            // so we create a new empty instance.
            $settings = new Settings([]);
        }

        $view = $this
            ->view()
            ->setData($settings)
        ;

        return $this->handleView($view);
    }

    /**
     * @param Request $request
     * @param string  $namespace
     *
     * @return Response
     */
    public function updateAction(Request $request, $namespace)
    {
        $this->isGrantedOr403($namespace);

        $manager = $this->getSettingsManager();

        try {
            $settings = $manager->loadSettings($namespace);
        } catch (MissingOptionsException $e) {
            // When it is the first time that a Settings is being persisted,
            // it won't have any initial value in database, so we should create a new instance.
            $settings = new Settings([]);
        }

        $isApiRequest = $this->isApiRequest($request);

        $form = $this
            ->getSettingsFormFactory()
            ->create($namespace, $settings, $isApiRequest ? ['csrf_protection' => false] : [])
        ;

        if ($form->handleRequest($request)->isValid()) {
            $messageType = 'success';
            try {
                $manager->saveSettings($namespace, $form->getData());
                $message = $this->getTranslator()->trans('sylius.settings.update', [], 'flashes');
            } catch (ValidatorException $exception) {
                $message = $this->getTranslator()->trans($exception->getMessage(), [], 'validators');
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

        return $this->render($request->attributes->get('template', 'SyliusSettingsBundle:Settings:update.html.twig'), [
            'settings' => $settings,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @return SettingsManagerInterface
     */
    protected function getSettingsManager()
    {
        return $this->get('sylius.settings.manager');
    }

    /**
     * @return SettingsFormFactoryInterface
     */
    protected function getSettingsFormFactory()
    {
        return $this->get('sylius.settings.form_factory');
    }

    /**
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
    }

    /**
     * @param Request $request
     *
     * @return bool
     */
    private function isApiRequest(Request $request)
    {
        return 'html' !== $request->getRequestFormat();
    }
}
