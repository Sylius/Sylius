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
     * Displays configuration page.
     *
     * @param Request
     *
     * @return Response
     */
    public function configureAction(Request $request, $namespace)
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
            $this->get('session')->getFlashBag()->add('success', 'Configuration has been saved');
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
        return $this->get('sylius_settings.manager');
    }

    /**
     * Get settings form factory.
     *
     * @return SettingsFormFactory
     */
    protected function getSettingsFormFactory()
    {
        return $this->get('sylius_settings.form.factory');
    }
}

