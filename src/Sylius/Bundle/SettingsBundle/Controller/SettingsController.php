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

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Settings controller.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class SettingsController extends Controller
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

        $form = $this
            ->getSettingsFormFactory()
            ->create($namespace)
        ;

        $form->setData($settings);

        if ($request->isMethod('POST') && $form->bind($request)->isValid()) {
            $manager->saveSettings($namespace, $form->getData());

            $message = $this->getTranslator()->trans('sylius.settings.update', array(), 'flashes');
            $this->get('session')->getFlashBag()->add('success', $message);
        }

        $template = $request->attributes->get('template');

        return $this->render($template, array(
            'settings' => $settings,
            'form'     => $form->createView()
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
     * @return SettingsFormFactory
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
}
