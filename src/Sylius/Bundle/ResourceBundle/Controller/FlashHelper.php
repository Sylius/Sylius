<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ResourceBundle\Controller;

use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Flashes helper.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@sylius.pl>
 */
class FlashHelper
{
    private $config;
    private $translator;
    private $session;

    public function __construct(Configuration $config, TranslatorInterface $translator, SessionInterface $session)
    {
        $this->config = $config;
        $this->translator = $translator;
        $this->session = $session;
    }

    public function setFlash($type, $event, $params = array())
    {
        return $this->session->getFlashBag()->add($type, $this->generateFlashMessage($event, $params));
    }

    private function generateFlashMessage($event, $params = array())
    {
        if (false === strpos($event, 'sylius.')) {
            $message = $this->config->getFlashMessage($event);
            $translatedMessage = $this->translateFlashMessage($message, $params);

            if ($message !== $translatedMessage) {
                return $translatedMessage;
            }

            return $this->translateFlashMessage('sylius.resource.'.$event, $params);
        }

        return $this->translateFlashMessage($event, $params);
    }

    private function translateFlashMessage($message, $params = array())
    {
        $resource = ucfirst(str_replace('_', ' ', $this->config->getResourceName()));

        return $this->translator->trans($message, array_merge(array('%resource%' => $resource), $params), 'flashes');
    }
}
