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
use Sylius\Bundle\SettingsBundle\Model\ParameterInterface;
use Sylius\Bundle\SettingsBundle\Schema\SettingsBuilder;
use Sylius\Bundle\SettingsBundle\Transformer\ParameterTransformerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * @author Steffen Brem <steffenbrem@gmail.com>
 */
class TransformParameterListener
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var ParameterInterface[]
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
        $parameter = $event->getObject();

        if ($parameter instanceof ParameterInterface) {
            $this->reverseTransform($parameter);
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
            if ($entity instanceof ParameterInterface) {
                $this->transform($entity);
            }
        }

        foreach ($uow->getScheduledEntityUpdates() as $entity) {
            if ($entity instanceof ParameterInterface) {
                $this->transform($entity);
            }
        }
    }

    public function postFlush()
    {
        foreach ($this->parametersMap as $map) {
            $map['entity']->setValue($map['value']);
        }

        // reset parameters map
        $this->parametersMap = [];
    }

    /**
     * @param ParameterInterface $parameter
     */
    protected function transform(ParameterInterface $parameter)
    {
        // store this parameter, so we can reverse it after flush
        $this->parametersMap[] = [
            'entity' => $parameter,
            'value' => $parameter->getValue(),
        ];

        if ($transformer = $this->getTransformer($parameter)) {
            $parameter->setValue($transformer->transform($parameter->getValue()));
        }
    }

    /**
     * @param ParameterInterface $parameter
     */
    protected function reverseTransform(ParameterInterface $parameter)
    {
        if ($transformer = $this->getTransformer($parameter)) {
            $parameter->setValue($transformer->reverseTransform($parameter->getValue()));
        }
    }

    /**
     * @param ParameterInterface $parameter
     *
     * @return ParameterTransformerInterface
     */
    protected function getTransformer(ParameterInterface $parameter)
    {
        $registry = $this->container->get('sylius.settings.schema_registry');
        $schema = $registry->getSchema($parameter->getSettings()->getSchema());

        $settingsBuilder = new SettingsBuilder();
        $schema->buildSettings($settingsBuilder);

        $transformers = $settingsBuilder->getTransformers();
        if (isset($transformers[$parameter->getName()])) {
            return $transformers[$parameter->getName()];
        }
    }
}
