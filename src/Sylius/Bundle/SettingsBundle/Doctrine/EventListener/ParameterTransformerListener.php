<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\SettingsBundle\Doctrine\EventListener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Sylius\Bundle\SettingsBundle\Model\SettingsInterface;
use Sylius\Bundle\SettingsBundle\Schema\SettingsBuilder;
use Sylius\Bundle\SettingsBundle\Transformer\ParameterTransformerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * @author Steffen Brem <steffenbrem@gmail.com>
 */
class ParameterTransformerListener
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var array
     */
    protected $parametersMap = [];

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @param LifecycleEventArgs $event
     */
    public function postLoad(LifecycleEventArgs $event)
    {
        $settings = $event->getObject();

        if ($settings instanceof SettingsInterface) {
            $this->reverseTransform($settings);
        }
    }

    /**
     * @param OnFlushEventArgs $event
     */
    public function onFlush(OnFlushEventArgs $event)
    {
        $em = $event->getEntityManager();
        $uow = $em->getUnitOfWork();

        foreach ($uow->getScheduledEntityInsertions() as $entity) {
            if ($entity instanceof SettingsInterface) {
                $this->transform($entity);
            }
        }

        foreach ($uow->getScheduledEntityUpdates() as $entity) {
            if ($entity instanceof SettingsInterface) {
                $this->transform($entity);
            }
        }
    }

    public function postFlush()
    {
        // revert settings parameters to what they were before flushing
        foreach ($this->parametersMap as $map) {
            $map['entity']->setParameters($map['parameters']);
        }

        // reset parameters map
        $this->parametersMap = [];
    }

    /**
     * @param SettingsInterface $settings
     */
    protected function transform(SettingsInterface $settings)
    {
        // store old parameters, so we can revert to it after flush
        $this->parametersMap[] = [
            'entity' => $settings,
            'parameters' => $settings->getParameters(),
        ];

        $transformers = $this->getTransformers($settings);
        foreach ($settings->getParameters() as $name => $value) {
            if (isset($transformers[$name])) {
                $settings->set($name, $transformers[$name]->transform($value));
            }
        }
    }

    /**
     * @param SettingsInterface $settings
     */
    protected function reverseTransform(SettingsInterface $settings)
    {
        $transformers = $this->getTransformers($settings);
        foreach ($settings->getParameters() as $name => $value) {
            if (isset($transformers[$name])) {
                $settings->set($name, $transformers[$name]->reverseTransform($value));
            }
        }
    }

    /**
     * @param SettingsInterface $settings
     *
     * @return ParameterTransformerInterface[]
     */
    protected function getTransformers(SettingsInterface $settings)
    {
        $registry = $this->container->get('sylius.registry.settings_schema');
        $schema = $registry->get($settings->getSchemaAlias());

        $settingsBuilder = new SettingsBuilder();
        $schema->buildSettings($settingsBuilder);

        return $settingsBuilder->getTransformers();
    }
}
