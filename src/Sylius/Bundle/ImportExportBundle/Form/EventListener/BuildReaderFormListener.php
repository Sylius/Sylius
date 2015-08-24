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

use Sylius\Component\ImportExport\Model\ProfileInterface;
use Sylius\Component\ImportExport\Reader\ReaderInterface;
use Sylius\Component\Registry\ServiceRegistryInterface;
use Sylius\Component\Resource\Exception\UnexpectedTypeException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
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

    /**
     * @param ServiceRegistryInterface $readerRegistry
     * @param FormFactoryInterface     $factory
     */
    public function __construct(ServiceRegistryInterface $readerRegistry, FormFactoryInterface $factory)
    {
        $this->readerRegistry = $readerRegistry;
        $this->factory = $factory;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            FormEvents::PRE_SET_DATA => 'preSetData',
            FormEvents::PRE_SUBMIT   => 'preBind',
        );
    }

    /**
     * @param FormEvent $event
     *
     * @throws UnexpectedTypeException
     */
    public function preSetData(FormEvent $event)
    {
        $profiler = $event->getData();

        if (null === $profiler) {
            return;
        }

        if (!$profiler instanceof ProfileInterface) {
            throw new UnexpectedTypeException($profiler, 'Sylius\Component\ImportExport\Model\ProfileInterface');
        }

        $this->addConfigurationFields($event->getForm(), $profiler->getReader(), $profiler->getReaderConfiguration());
    }

    /**
     * @param FormEvent $event
     */
    public function preBind(FormEvent $event)
    {
        $data = $event->getData();

        if (empty($data) || !array_key_exists('reader', $data)) {
            return;
        }

        $this->addConfigurationFields($event->getForm(), $data['reader']);
    }

    /**
     * @param FormInterface $form
     * @param string        $type
     * @param array         $configuration
     */
    protected function addConfigurationFields(FormInterface $form, $type, array $configuration = array())
    {
        $reader = $this->getReader($type);
        $formType = sprintf('sylius_%s_reader', $reader->getType());

        $configurationField = $this->factory->createNamed(
            'readerConfiguration',
            $formType,
            $configuration,
            array('auto_initialize' => false)
        );

        $form->add($configurationField);
    }

    /**
     * @param string $type
     *
     * @return ReaderInterface
     */
    protected function getReader($type)
    {
        if (null === $type) {
            $registry = $this->readerRegistry->all();

            return reset($registry);
        }

        return $this->readerRegistry->get($type);
    }
}
