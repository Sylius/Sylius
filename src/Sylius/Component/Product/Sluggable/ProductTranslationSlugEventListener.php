<?php

namespace Sylius\Component\Product\Sluggable;

use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\Persistence\ObjectRepository;
use Sylius\Component\Product\Model\ProductInterface;
use Sylius\Component\Product\Model\ProductTranslationInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
class ProductTranslationSlugEventListener
{
    /**
     * @var array
     */
    private $generatedSlugs = [];

    /**
     * @param LifecycleEventArgs $lifecycleEventArgs
     */
    public function prePersist(LifecycleEventArgs $lifecycleEventArgs)
    {
        if ($this->isSluggable($lifecycleEventArgs->getObject())) {
            $this->updateSlug($lifecycleEventArgs);
        }
    }

    /**
     * @param LifecycleEventArgs $lifecycleEventArgs
     */
    public function preUpdate(LifecycleEventArgs $lifecycleEventArgs)
    {
        if ($this->isSluggable($lifecycleEventArgs->getObject())) {
            $this->updateSlug($lifecycleEventArgs);
        }
    }

    /**
     * @param object $object
     *
     * @return bool
     */
    private function isSluggable($object)
    {
        return $object instanceof ProductTranslationInterface && !$object instanceof ProductInterface;
    }

    /**
     * @param LifecycleEventArgs $lifecycleEventArgs
     */
    private function updateSlug(LifecycleEventArgs $lifecycleEventArgs)
    {
        $object = $lifecycleEventArgs->getObject();
        $productTranslationRepository = $lifecycleEventArgs->getObjectManager()->getRepository(get_class($object));

        $slug = $this->slugify($object->getName(), $productTranslationRepository);

        $object->setSlug($slug);
        $this->generatedSlugs[] = $slug;
    }

    /**
     * @param string $string
     * @param ObjectRepository $productTranslationRepository
     *
     * @return string
     */
    private function slugify($string, ObjectRepository $productTranslationRepository)
    {
        $baseSlug = iconv('UTF-8', 'ASCII//TRANSLIT', $string);
        $baseSlug = preg_replace("/[^a-zA-Z0-9\/_|+ -]/", '', $baseSlug);
        $baseSlug = strtolower(trim($baseSlug, '-'));
        $baseSlug = preg_replace('/[\\\\\\/_|+ -]+/', '-', $baseSlug);

        $slug = $baseSlug;
        for ($i = 1; $this->isAlreadyUsed($slug, $productTranslationRepository); ++$i) {
            $slug = $baseSlug . '-' . $i;
        }

        return $slug;
    }

    /**
     * @param string $slug
     * @param ObjectRepository $productTranslationRepository
     *
     * @return bool
     */
    private function isAlreadyUsed($slug, ObjectRepository $productTranslationRepository)
    {
        if (in_array($slug, $this->generatedSlugs)) {
            return true;
        }

        if (null !== $productTranslationRepository->findOneBy(['slug' => $slug])) {
            return true;
        }

        return false;
    }
}
