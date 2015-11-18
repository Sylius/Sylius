<?php

namespace Sylius\Component\Product\Sluggable;

use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use Sylius\Component\Product\Model\ProductTranslationInterface;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
class ProductTranslationSlugEventListener
{
    /**
     * @param LifecycleEventArgs $eventArgs
     */
    public function prePersist(LifecycleEventArgs $eventArgs)
    {
        $object = $eventArgs->getObject();

        if ($this->isSluggable($object)) {
            $this->updateSlug($object);
        }
    }

    /**
     * @param LifecycleEventArgs $eventArgs
     */
    public function preUpdate(LifecycleEventArgs $eventArgs)
    {
        $object = $eventArgs->getObject();

        if ($this->isSluggable($object)) {
            $this->updateSlug($object);
        }
    }

    /**
     * @param object $object
     *
     * @return bool
     */
    private function isSluggable($object)
    {
        return $object instanceof ProductTranslationInterface;
    }

    /**
     * @param ProductTranslationInterface $object
     */
    private function updateSlug(ProductTranslationInterface $object)
    {
        $slug = $this->slugify($object->getName());

        $object->setSlug($slug);
    }

    /**
     * @param string $string
     *
     * @return string
     */
    private function slugify($string)
    {
        $clean = iconv('UTF-8', 'ASCII//TRANSLIT', $string);
        $clean = preg_replace("/[^a-zA-Z0-9\/_|+ -]/", '', $clean);
        $clean = strtolower(trim($clean, '-'));
        $clean = preg_replace('/[\\\\\\/_|+ -]+/', '-', $clean);

        return $clean;
    }
}
