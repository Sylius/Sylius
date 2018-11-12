<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\ResourceBundle\Controller;

use Doctrine\Common\Inflector\Inflector;
use Sylius\Bundle\ResourceBundle\Event\ResourceControllerEvent;
use Sylius\Component\Resource\Model\ResourceInterface;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Translation\TranslatorBagInterface;
use Symfony\Component\Translation\TranslatorInterface;

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

    public function __construct(SessionInterface $session, TranslatorInterface $translator, string $defaultLocale)
    {
        $this->session = $session;
        $this->translator = $translator;
        $this->defaultLocale = $defaultLocale;
    }

    /**
     * {@inheritdoc}
     */
    public function addSuccessFlash(
        RequestConfiguration $requestConfiguration,
        string $actionName,
        ?ResourceInterface $resource = null
    ): void {
        $this->addFlashWithType($requestConfiguration, $actionName, 'success');
    }

    /**
     * {@inheritdoc}
     */
    public function addErrorFlash(RequestConfiguration $requestConfiguration, string $actionName): void
    {
        $this->addFlashWithType($requestConfiguration, $actionName, 'error');
    }

    /**
     * {@inheritdoc}
     */
    public function addFlashFromEvent(RequestConfiguration $requestConfiguration, ResourceControllerEvent $event): void
    {
        $this->addFlash($event->getMessageType(), $event->getMessage(), $event->getMessageParameters());
    }

    private function addFlashWithType(RequestConfiguration $requestConfiguration, string $actionName, string $type): void
    {
        $metadata = $requestConfiguration->getMetadata();
        $metadataName = ucfirst($metadata->getHumanizedName());
        $parameters = $this->getParametersWithName($metadataName, $actionName);

        $message = (string) $requestConfiguration->getFlashMessage($actionName);
        if (empty($message)) {
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

    private function addFlash(string $type, string $message, array $parameters = []): void
    {
        if (!empty($parameters)) {
            $message = $this->prepareMessage($message, $parameters);
        }

        /** @var FlashBagInterface $flashBag */
        $flashBag = $this->session->getBag('flashes');
        $flashBag->add($type, $message);
    }

    private function prepareMessage(string $message, array $parameters): array
    {
        return [
            'message' => $message,
            'parameters' => $parameters,
        ];
    }

    private function getResourceMessage(string $actionName): string
    {
        return sprintf('sylius.resource.%s', $actionName);
    }

    private function isTranslationDefined(string $message, string $locale, array $parameters): bool
    {
        if ($this->translator instanceof TranslatorBagInterface) {
            $defaultCatalogue = $this->translator->getCatalogue($locale);

            return $defaultCatalogue->has($message, 'flashes');
        }

        return $message !== $this->translator->trans($message, $parameters, 'flashes');
    }

    private function getParametersWithName(string $metadataName, string $actionName): array
    {
        if (stripos($actionName, 'bulk') !== false) {
            return ['%resources%' => ucfirst(Inflector::pluralize($metadataName))];
        }

        return ['%resource%' => ucfirst($metadataName)];
    }
}
