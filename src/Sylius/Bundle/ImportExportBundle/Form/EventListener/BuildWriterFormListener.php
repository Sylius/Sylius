<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ImportExportBundle\Form\EventListener;

use Sylius\Component\ImportExport\Model\ExportProfileInterface;
use Sylius\Component\ImportExport\Model\ProfileInterface;
use Sylius\Component\Registry\ServiceRegistryInterface;
use Sylius\Component\Resource\Exception\UnexpectedTypeException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;

/**
 * This listener adds configuration form to a export profile,
 * if selected profile requires one.
 *
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 * @author Bartosz Siejka <bartosz.siejka@lakion.com>
 */
class BuildWriterFormListener implements EventSubscriberInterface
{
    /**
     * @var ServiceRegistryInterface
     */
    private $writerRegistry;

    /**
     * @var FormFactoryInterface
     */
    private $factory;

    public function __construct(ServiceRegistryInterface $writerRegistry, FormFactoryInterface $factory)
    {
        $this->writerRegistry = $writerRegistry;
        $this->factory = $factory;
    }

    public static function getSubscribedEvents()
    {
        return array(
            FormEvents::PRE_SET_DATA => 'preSetData',
            FormEvents::PRE_SUBMIT   => 'preBind',
        );
    }

    public function preSetData(FormEvent $event)
    {
        $profiler = $event->getData();

        if (null === $profiler) {
            return;
        }

        if (!$profiler instanceof ProfileInterface) {
            throw new UnexpectedTypeException($profiler, 'Sylius\Component\ImportExport\Model\ProfileInterface');
        }

        $this->addConfigurationFields($event->getForm(), $profiler->getWriter(), $profiler->getWriterConfiguration());
    }

    public function preBind(FormEvent $event)
    {
        $data = $event->getData();

        if (empty($data) || !array_key_exists('writer', $data)) {
            return;
        }

        $this->addConfigurationFields($event->getForm(), $data['writer']);
    }

    protected function addConfigurationFields(FormInterface $form, $type, array $configuration = array())
    {
        $writer = $this->writerRegistry->get($type);
        $formType = sprintf('sylius_%s_writer', $writer->getType());
        try {
            $configurationField = $this->factory->createNamed(
                'writerConfiguration',
                $formType,
                $configuration,
                array('auto_initialize' => false)
            );
        } catch (\InvalidArgumentException $e) {
            return;
        }
        $form->add($configurationField);
    }
}
