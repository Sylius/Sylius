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
        $schemaAlias = $request->attributes->get('schema');

        $this->isGrantedOr403($schemaAlias);

        $settings = $this->getSettingsManager()->load($schemaAlias);

        $view = $this
            ->view()
            ->setData($settings)
        ;

        return $this->handleView($view);
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function updateAction(Request $request)
    {
        $schemaAlias = $request->attributes->get('schema');

        $this->isGrantedOr403($schemaAlias);

        $settingsManager = $this->getSettingsManager();
        $settings = $settingsManager->load($schemaAlias);

        $isApiRequest = $this->isApiRequest($request);

        $form = $this
            ->getSettingsFormFactory()
            ->create($schemaAlias, $settings, $isApiRequest ? ['csrf_protection' => false] : [])
        ;

        if ($form->handleRequest($request)->isValid()) {
            $messageType = 'success';
            try {
                $settingsManager->save($settings);
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
     * Check that user can change given schema.
     *
     * @param string $schemaAlias
     *
     * @return bool
     */
    protected function isGrantedOr403($schemaAlias)
    {
        if (!$this->container->has('sylius.authorization_checker')) {
            return true;
        }

        if (!$this->get('sylius.authorization_checker')->isGranted(sprintf('sylius.settings.%s', $schemaAlias))) {
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
