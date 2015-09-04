<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ResourceBundle\EventListener;

use Sylius\Bundle\ResourceBundle\Event\GenericResourceEvent;
use Sylius\Component\Resource\Metadata\ResourceMetadataInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class FlashesListener
{
    const FLASHES_BAG = 'flashes';
    const TRANSLATION_DOMAIN = 'flashes';

    /**
     * @var SessionInterface
     */
    private $session;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @param SessionInterface $session
     * @param $translator
     */
    public function __construct(SessionInterface $session, $translator)
    {
        $this->session = $session;
        $this->translator = $translator;
    }

    /**
     * @param GenericResourceEvent $event
     */
    public function addSuccessFlash(GenericResourceEvent $event)
    {
        $this->addFlash('success', $this->getMessage($event));
    }

    /**
     * @param string $type
     * @param string $message
     */
    private function addFlash($type, $message)
    {
        $this->session->getBag(self::FLASHES_BAG)->add($type, $message);
    }

    /**
     * @param GenericResourceEvent $event
     *
     * @return string
     */
    private function getMessage(GenericResourceEvent $event)
    {
        $metadata = $event->getMetadata();
        $requestConfiguration = $event->getRequestConfiguration();

        $defaultMessage = sprintf('%s.%s.%s', $metadata->getApplicationName(), $metadata->getResourceName(), $event->getActionName());
        $message = $requestConfiguration->getFlashMessage() ?: $defaultMessage;

        $translatedMessage = $this->translator->trans($message, array(), self::TRANSLATION_DOMAIN);

        if ($translatedMessage === $message) {
            $translatedMessage = $this->translator->trans('sylius.resource.'.$event->getActionName(), array('%resource%' => $this->getHumanizedResourceName($metadata)), self::TRANSLATION_DOMAIN);
        }

        return $translatedMessage;
    }

    /**
     * @param ResourceMetadataInterface $metadata
     *
     * @return string
     */
    private function getHumanizedResourceName(ResourceMetadataInterface $metadata)
    {
        return $resource = ucfirst(str_replace('_', ' ', $metadata->getResourceName()));
    }
}
