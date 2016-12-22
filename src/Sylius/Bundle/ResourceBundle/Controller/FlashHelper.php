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

use Sylius\Bundle\ResourceBundle\Event\ResourceControllerEvent;
use Sylius\Component\Resource\Model\ResourceInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Translation\TranslatorBagInterface;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 * @author Jan Góralski <jan.goralski@lakion.com>
 */
final class FlashHelper implements FlashHelperInterface
{
    /**
     * @var SessionInterface
     */
    private $session;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var string
     */
    private $defaultLocale;

    /**
     * @param SessionInterface $session
     * @param TranslatorInterface $translator
     * @param string $defaultLocale
     */
    public function __construct(SessionInterface $session, TranslatorInterface $translator, $defaultLocale)
    {
        $this->session = $session;
        $this->translator = $translator;
        $this->defaultLocale = $defaultLocale;
    }

    /**
     * {@inheritdoc}
     */
    public function addSuccessFlash(RequestConfiguration $requestConfiguration, $actionName, ResourceInterface $resource = null)
    {
        $metadata = $requestConfiguration->getMetadata();
        $metadataName = $metadata->getHumanizedName();

        $message = $requestConfiguration->getFlashMessage($actionName);
        if (false === $message) {
            return;
        }

        if ($this->isTranslationDefined($message, $this->defaultLocale)) {
            $this->addFlash('success', $message);

            return;
        }

        $this->addFlash(
            'success',
            $this->getResourceMessage($actionName),
            ['%resource%' => ucfirst($metadataName)]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function addFlashFromEvent(RequestConfiguration $requestConfiguration, ResourceControllerEvent $event)
    {
        $this->addFlash($event->getMessageType(), $event->getMessage(), $event->getMessageParameters());
    }

    /**
     * @param string $type
     * @param string $message
     * @param array $parameters
     */
    private function addFlash($type, $message, array $parameters = [])
    {
        if (!empty($parameters)) {
            $message = $this->prepareMessage($message, $parameters);
        }

        $this->session->getBag('flashes')->add($type, $message);
    }

    /**
     * @param string $message
     * @param array $parameters
     *
     * @return array
     */
    private function prepareMessage($message, array $parameters)
    {
        return [
            'message' => $message,
            'parameters' => $parameters,
        ];
    }

    /**
     * @param string $actionName
     *
     * @return string
     */
    private function getResourceMessage($actionName)
    {
        return sprintf('sylius.resource.%s', $actionName);
    }

    /**
     * @param string $message
     * @param string $locale
     *
     * @return bool
     */
    private function isTranslationDefined($message, $locale)
    {
        if ($this->translator instanceof TranslatorBagInterface) {
            $defaultCatalogue = $this->translator->getCatalogue($locale);

            return $defaultCatalogue->has($message, 'flashes');
        }

        return false;
    }
}
