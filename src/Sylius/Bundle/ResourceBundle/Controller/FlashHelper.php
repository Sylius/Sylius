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
        $this->addFlashWithType($requestConfiguration, $actionName, 'success');
    }

    /**
     * {@inheritdoc}
     */
    public function addErrorFlash(RequestConfiguration $requestConfiguration, $actionName)
    {
        $this->addFlashWithType($requestConfiguration, $actionName, 'error');
    }

    /**
     * {@inheritdoc}
     */
    public function addFlashFromEvent(RequestConfiguration $requestConfiguration, ResourceControllerEvent $event)
    {
        $this->addFlash($event->getMessageType(), $event->getMessage(), $event->getMessageParameters());
    }

    /**
     * @param RequestConfiguration $requestConfiguration
     * @param string $actionName
     * @param string $type
     */
    private function addFlashWithType(RequestConfiguration $requestConfiguration, $actionName, $type)
    {
        $metadata = $requestConfiguration->getMetadata();
        $metadataName = ucfirst($metadata->getHumanizedName());
        $parameters = ['%resource%' => $metadataName];

        $message = $requestConfiguration->getFlashMessage($actionName);
        if (false === $message) {
            return;
        }

        if ($this->isTranslationDefined($message, $this->defaultLocale, $parameters)) {
            if (!$this->translator instanceof TranslatorBagInterface) {
                $this->addFlash($type, $message, $parameters);

                return;
            }

            $this->addFlash($type, $message);

            return;
        }

        $this->addFlash(
            $type,
            $this->getResourceMessage($actionName),
            $parameters
        );
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
     * @param array $parameters
     *
     * @return bool
     */
    private function isTranslationDefined($message, $locale, array $parameters)
    {
        if ($this->translator instanceof TranslatorBagInterface) {
            $defaultCatalogue = $this->translator->getCatalogue($locale);

            return $defaultCatalogue->has($message, 'flashes');
        }

        return $message !== $this->translator->trans($message, $parameters,'flashes');
    }
}
