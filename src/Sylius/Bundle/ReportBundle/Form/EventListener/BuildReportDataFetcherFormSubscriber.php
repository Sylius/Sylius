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
 * if selected data fetcher requires one.
 *
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
class BuildReportDataFetcherFormSubscriber implements EventSubscriberInterface
{
    /**
     * @var ServiceRegistryInterface
     */
    private $dataFetcherRegistry;

    /**
     * @var FormFactoryInterface
     */
    private $factory;

    public function __construct(ServiceRegistryInterface $dataFetcherRegistry, FormFactoryInterface $factory)
    {
        $this->dataFetcherRegistry = $dataFetcherRegistry;
        $this->factory = $factory;
    }

    /**
     * {@inheritdoc}
     */
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

        $this->addConfigurationFields($event->getForm(), $report->getDataFetcher(), $report->getDataFetcherConfiguration());
    }

    public function preBind(FormEvent $event)
    {
        $data = $event->getData();

        if (empty($data) || !array_key_exists('dataFetcher', $data)) {
            return;
        }

        $this->addConfigurationFields($event->getForm(), $data['dataFetcher']);
    }

    /**
     * Add configuration fields to the form.
     *
     * @param FormInterface $form
     * @param string        $dataFetcherType
     * @param array         $config
     */
    protected function addConfigurationFields(FormInterface $form, $dataFetcherType, array $config = [])
    {
        $dataFetcher = $this->dataFetcherRegistry->get($dataFetcherType);
        $formType = $dataFetcher->getType();

        try {
            $configurationField = $this->factory->createNamed(
                'dataFetcherConfiguration',
                $formType,
                $config,
                ['auto_initialize' => false]
            );
        } catch (\InvalidArgumentException $e) {
            return;
        }

        $form->add($configurationField);
    }
}
