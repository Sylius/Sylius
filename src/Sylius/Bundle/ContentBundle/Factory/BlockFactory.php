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
use Sylius\Component\Resource\Factory\FactoryInterface;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class BlockFactory implements FactoryInterface
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
    private $blockParentPath;

    /**
     * @param FactoryInterface $decoratedFactory
     * @param ObjectManager $documentManager
     * @param string $blockParentPath
     */
    public function __construct(FactoryInterface $decoratedFactory, ObjectManager $documentManager, $blockParentPath)
    {
        $this->decoratedFactory = $decoratedFactory;
        $this->documentManager = $documentManager;
        $this->blockParentPath = $blockParentPath;
    }

    /**
     * {@inheritdoc}
     */
    public function createNew()
    {
        $block = $this->decoratedFactory->createNew();
        $block->setParentDocument($parent = $this->documentManager->find(null, $this->blockParentPath));

        return $block;
    }
}
