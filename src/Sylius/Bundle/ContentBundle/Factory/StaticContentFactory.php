<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ContentBundle\Factory;

use Doctrine\Common\Persistence\ObjectManager;
use Sylius\Bundle\ContentBundle\Document\StaticContent;
use Sylius\Component\Resource\Factory\FactoryInterface;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class StaticContentFactory implements FactoryInterface
{
    /**
     * @var FactoryInterface
     */
    private $decoratedFactory;

    /**
     * @var ObjectManager
     */
    private $documentManager;

    /**
     * @var string
     */
    private $staticContentParentPath;

    /**
     * @param FactoryInterface $decoratedFactory
     * @param ObjectManager $documentManager
     * @param string $staticContentParentPath
     */
    public function __construct(FactoryInterface $decoratedFactory, ObjectManager $documentManager, $staticContentParentPath)
    {
        $this->decoratedFactory = $decoratedFactory;
        $this->documentManager = $documentManager;
        $this->staticContentParentPath = $staticContentParentPath;
    }

    /**
     * {@inheritdoc}
     */
    public function createNew()
    {
        /** @var StaticContent $staticContent */
        $staticContent = $this->decoratedFactory->createNew();
        $staticContent->setParentDocument($this->documentManager->find(null, $this->staticContentParentPath));

        return $staticContent;
    }
}
