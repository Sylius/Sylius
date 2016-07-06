<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\FixturesBundle\DataFixtures\ORM;

use Doctrine\Common\Persistence\ObjectManager;
use Sylius\Bundle\FixturesBundle\DataFixtures\DataFixture;
use Sylius\Bundle\MetadataBundle\Model\MetadataContainer;
use Sylius\Component\Metadata\Model\Custom\Page;
use Sylius\Component\Metadata\Model\Custom\PageMetadata;
use Sylius\Component\Metadata\Model\MetadataType;

/**
 * @author Pete Ward <peter.ward@reiss.com>
 */
class LoadMetadataData extends DataFixture
{
    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager)
    {
        $metadata = new PageMetadata();
        $metadata->setTitle('Sylius - Modern ecommerce for Symfony2');
        $metadata->setDescription('Sylius is a modern ecommerce solution for PHP. Based on the Symfony2 framework.');
        $metadata->setKeywords(['symfony', 'sylius', 'ecommerce', 'webshop', 'shopping cart']);

        $metadataContainer = new MetadataContainer();
        $metadataContainer->setType(MetadataType::PAGE);
        $metadataContainer->setCode(Page::METADATA_CLASS_IDENTIFIER);
        $metadataContainer->setMetadata($metadata);

        $manager->persist($metadataContainer);
        $manager->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function getOrder()
    {
        return 50;
    }
}