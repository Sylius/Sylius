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
 * This listener adds configuration form to an export profile,
 * if selected profile requires one.
 *
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
class BuildReaderFormListener implements EventSubscriberInterface
{
    /**
     * @var ServiceRegistryInterface
     */
    private $readerRegistry;

    /**
     * @var FormFactoryInterface
     */
    private $factory;

    public function __construct(ServiceRegistryInterface $readerRegistry, FormFactoryInterface $factory)
    {
        $this->readerRegistry = $readerRegistry;
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

        $this->addConfigurationFields($event->getForm(), $exportProfiler->getReader(), $exportProfiler->getReaderConfiguration());
    }

    public function preBind(FormEvent $event)
    {
        $data = $event->getData();

        if (empty($data) || !array_key_exists('reader', $data)) {
            return;
        }

        $this->addConfigurationFields($event->getForm(), $data['reader']);
    }

    protected function addConfigurationFields(FormInterface $form, $exporterType, array $configuration = array())
    {
        $exporter = $this->readerRegistry->get($exporterType);
        $formType = sprintf('sylius_%s_reader', $exporter->getType());
        try {
            $configurationField = $this->factory->createNamed(
                'readerConfiguration', 
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
