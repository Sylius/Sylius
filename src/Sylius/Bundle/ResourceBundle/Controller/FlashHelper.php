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
use Sylius\Component\Resource\Metadata\MetadataInterface;
use Sylius\Component\Resource\Model\ResourceInterface;
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

    /**
     * @param SessionInterface $session
     * @param TranslatorInterface $translator
     * @param string $defaultLocale
     */
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

    /**
     * @param RequestConfiguration $requestConfiguration
     * @param string $actionName
     * @param string $type
     */
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

    /**
     * @param string $type
     * @param string $message
     * @param array $parameters
     */
    private function addFlash(string $type, string $message, array $parameters = []): void
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
    private function prepareMessage(string $message, array $parameters): array
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
    private function getResourceMessage(string $actionName): string
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
    private function isTranslationDefined(string $message, string $locale, array $parameters): bool
    {
        if ($this->translator instanceof TranslatorBagInterface) {
            $defaultCatalogue = $this->translator->getCatalogue($locale);

            return $defaultCatalogue->has($message, 'flashes');
        }

        return $message !== $this->translator->trans($message, $parameters, 'flashes');
    }

    /**
     * @param string $metadataName
     * @param string $actionName
     *
     * @return array
     */
    private function getParametersWithName(string $metadataName, string $actionName): array
    {
        if (stripos($actionName, 'bulk') !== false) {
            return ['%resources%' => ucfirst(Inflector::pluralize($metadataName))];
        }

        return ['%resource%' => ucfirst($metadataName)];
    }
}
