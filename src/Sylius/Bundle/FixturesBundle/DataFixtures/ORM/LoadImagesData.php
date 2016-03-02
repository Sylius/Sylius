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
use Symfony\Component\Finder\Finder;

/**
 * Default product images.
 *
 * @author Saša Stamenković <umpirsky@gmail.com>
 * @author Aram Alipor <aram.alipoor@gmail.com>
 */
class LoadImagesData extends DataFixture
{
    protected $path = '/../../Resources/fixtures';

    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager)
    {
        $finder = new Finder();
        $documentManager = $this->get('doctrine_phpcr.odm.document_manager');
        $mediaBase = $this->container->getParameter('cmf_media.persistence.phpcr.media_basepath');

        foreach ($finder->files()->in(__DIR__.$this->path) as $imageFile) {
            $media = $documentManager->find(null, $mediaBase.'/'.$imageFile->getFilename());

            $image = $this->getImageFactory()->createNew();
            $image->setMedia($media);
            $manager->persist($image);

            $this->setReference('Sylius.Image.'.$imageFile->getBasename('.jpg'), $image);
        }

        $manager->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function getOrder()
    {
        return 10;
    }
}
