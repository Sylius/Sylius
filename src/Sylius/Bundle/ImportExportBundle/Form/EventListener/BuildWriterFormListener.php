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
            FormEvents::PRE_SUBMIT   => 'preBind'
        );
    }

    public function preSetData(FormEvent $event)
    {
        $exportProfiler = $event->getData();

        if (null === $exportProfiler) {
            return;
        }

        if (!$exportProfiler instanceof ExportProfileInterface) {
            throw new UnexpectedTypeException($exportProfiler, 'Sylius\Component\ImportExport\Model\ExportProfileInterface');
        }

        $this->addConfigurationFields($event->getForm(), $exportProfiler->getWriter(), $exportProfiler->getWriterConfiguration());
    }

    public function preBind(FormEvent $event)
    {
        $data = $event->getData();

        if (empty($data) || !array_key_exists('writer', $data)) {
            return;
        }

        $this->addConfigurationFields($event->getForm(), $data['writer']);
    }

    protected function addConfigurationFields(FormInterface $form, $exporterType, array $configuration = array())
    {
        $exporter = $this->writerRegistry->get($exporterType);
        $formType = sprintf('sylius_%s_writer', $exporter->getType());
        try {
            $configurationField = $this->factory->createNamed(
                'writerConfiguration', 
                $formType, 
                $configuration, 
                array('auto_initialize' => false)
            );
        } catch (\InvalidArgumentException $e){
            return;
        }
        $form->add($configurationField);
    }
}
