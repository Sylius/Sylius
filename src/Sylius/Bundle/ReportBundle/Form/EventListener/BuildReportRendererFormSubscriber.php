<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ReportBundle\Form\EventListener;

use Sylius\Component\Registry\ServiceRegistryInterface;
use Sylius\Component\Report\Model\ReportInterface;
use Sylius\Component\Resource\Exception\UnexpectedTypeException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;

/**
 * This listener adds configuration form to the report object
 * if selected renderer requires one.
 *
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
class BuildReportRendererFormSubscriber implements EventSubscriberInterface
{
    /**
     * @var ServiceRegistryInterface
     */
    private $rendererRegistry;

    /**
     * @var FormFactoryInterface
     */
    private $factory;

    public function __construct(ServiceRegistryInterface $rendererRegistry, FormFactoryInterface $factory)
    {
        $this->rendererRegistry = $rendererRegistry;
        $this->factory = $factory;
    }

    public static function getSubscribedEvents()
    {
        return [
            FormEvents::PRE_SET_DATA => 'preSetData',
            FormEvents::PRE_SUBMIT => 'preBind',
        ];
    }

    public function preSetData(FormEvent $event)
    {
        $report = $event->getData();

        if (null === $report) {
            return;
        }

        if (!$report instanceof ReportInterface) {
            throw new UnexpectedTypeException($report, ReportInterface::class);
        }

        $this->addConfigurationFields($event->getForm(), $report->getRenderer(), $report->getRendererConfiguration());
    }

    public function preBind(FormEvent $event)
    {
        $data = $event->getData();

        if (empty($data) || !array_key_exists('renderer', $data)) {
            return;
        }

        $this->addConfigurationFields($event->getForm(), $data['renderer']);
    }

    /**
     * Add configuration fields to the form.
     *
     * @param FormInterface $form
     * @param string        $rendererType
     * @param array         $data
     */
    public function addConfigurationFields(FormInterface $form, $rendererType, array $data = [])
    {
        $renderer = $this->rendererRegistry->get($rendererType);
        $formType = $renderer->getType();

        try {
            $configurationField = $this->factory->createNamed(
                'rendererConfiguration',
                $formType,
                $data,
                ['auto_initialize' => false]
            );
        } catch (\InvalidArgumentException $e) {
            return;
        }

        $form->add($configurationField);
    }
}
