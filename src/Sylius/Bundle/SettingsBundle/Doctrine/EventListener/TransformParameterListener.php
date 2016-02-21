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

use Doctrine\ORM\Event\OnFlushEventArgs;
use Sylius\Bundle\SettingsBundle\Model\ParameterInterface;
use Sylius\Bundle\SettingsBundle\Schema\SettingsBuilder;
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

        $registry = $this->container->get('sylius.settings.schema_registry');
        $schema = $registry->getSchema($parameter->getSettings()->getSchema());

        $settingsBuilder = new SettingsBuilder();
        $schema->buildSettings($settingsBuilder);

        $transformers = $settingsBuilder->getTransformers();
        if (isset($transformers[$parameter->getName()])) {
            $transformer = $transformers[$parameter->getName()];
            $parameter->setValue($transformer->transform($parameter->getValue()));
        }
    }
}
