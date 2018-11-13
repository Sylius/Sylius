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

namespace Sylius\Bundle\ResourceBundle\Form\EventSubscriber;

use Sylius\Component\Resource\Exception\UnexpectedTypeException;
use Sylius\Component\Resource\Model\CodeAwareInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

final class AddCodeFormSubscriber implements EventSubscriberInterface
{
    /**
     * @var string
     */
    private $type;

    /**
     * @var array
     */
    private $options;

    /**
     * @param string $type
     */
    public function __construct(?string $type = null, array $options = [])
    {
        $this->type = $type ?? TextType::class;
        $this->options = $options;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents(): array
    {
        return [
            FormEvents::PRE_SET_DATA => 'preSetData',
        ];
    }

    public function preSetData(FormEvent $event): void
    {
        $resource = $event->getData();
        $disabled = false;

        if ($resource instanceof CodeAwareInterface) {
            $disabled = null !== $resource->getCode();
        } elseif (null !== $resource) {
            throw new UnexpectedTypeException($resource, CodeAwareInterface::class);
        }

        $form = $event->getForm();
        $form->add('code', $this->type, array_merge(
            ['label' => 'sylius.ui.code'],
            $this->options,
            ['disabled' => $disabled]
        ));
    }
}
