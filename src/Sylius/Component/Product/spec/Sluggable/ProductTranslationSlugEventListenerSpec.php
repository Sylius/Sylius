<?php

namespace spec\Sylius\Component\Product\Sluggable;

use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\Persistence\ObjectRepository;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Component\Product\Model\ProductTranslationInterface;

/**
 * @mixin \Sylius\Component\Product\Sluggable\ProductTranslationSlugEventListener
 *
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
class ProductTranslationSlugEventListenerSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Product\Sluggable\ProductTranslationSlugEventListener');
    }

    function it_generates_slug_for_profuct_translation_when_inserting(
        LifecycleEventArgs $lifecycleEventArgs,
        ObjectManager $objectManager,
        ProductTranslationInterface $productTranslation,
        ObjectRepository $productTranslationRepository
    ) {
        $lifecycleEventArgs->getObjectManager()->willReturn($objectManager);
        $objectManager->getRepository(get_class($productTranslation->getWrappedObject()))->willReturn($productTranslationRepository);

        $lifecycleEventArgs->getObject()->willReturn($productTranslation);
        $productTranslation->getName()->willReturn('Banana');

        $productTranslationRepository->findOneBy(['slug' => 'banana'])->willReturn(null);

        $productTranslation->setSlug('banana')->shouldBeCalled();

        $this->prePersist($lifecycleEventArgs);
    }

    function it_generates_slug_for_profuct_translation_when_updating(
        LifecycleEventArgs $lifecycleEventArgs,
        ObjectManager $objectManager,
        ProductTranslationInterface $productTranslation,
        ObjectRepository $productTranslationRepository
    ) {
        $lifecycleEventArgs->getObjectManager()->willReturn($objectManager);
        $objectManager->getRepository(get_class($productTranslation->getWrappedObject()))->willReturn($productTranslationRepository);

        $lifecycleEventArgs->getObject()->willReturn($productTranslation);
        $productTranslation->getName()->willReturn('Banana');

        $productTranslationRepository->findOneBy(['slug' => 'banana'])->willReturn(null);

        $productTranslation->setSlug('banana')->shouldBeCalled();

        $this->preUpdate($lifecycleEventArgs);
    }

    function it_does_not_generate_duplicated_slugs_based_on_internal_memory_when_inserting(
        ObjectManager $objectManager,
        ObjectRepository $productTranslationRepository,
        LifecycleEventArgs $firstLifecycleEventArgs,
        ProductTranslationInterface $firstProductTranslation,
        LifecycleEventArgs $secondLifecycleEventArgs,
        ProductTranslationInterface $secondProductTranslation
    ) {
        $firstLifecycleEventArgs->getObjectManager()->willReturn($objectManager);
        $secondLifecycleEventArgs->getObjectManager()->willReturn($objectManager);

        $objectManager->getRepository(get_class($firstProductTranslation->getWrappedObject()))->willReturn($productTranslationRepository);
        $objectManager->getRepository(get_class($secondProductTranslation->getWrappedObject()))->willReturn($productTranslationRepository);

        $firstLifecycleEventArgs->getObject()->willReturn($firstProductTranslation);
        $secondLifecycleEventArgs->getObject()->willReturn($secondProductTranslation);

        $firstProductTranslation->getName()->willReturn('Banana');
        $secondProductTranslation->getName()->willReturn('Banana');

        $productTranslationRepository->findOneBy(['slug' => 'banana'])->willReturn(null);
        $productTranslationRepository->findOneBy(['slug' => 'banana-1'])->willReturn(null);

        $firstProductTranslation->setSlug('banana')->shouldBeCalled();
        $secondProductTranslation->setSlug('banana-1')->shouldBeCalled();

        $this->prePersist($firstLifecycleEventArgs);
        $this->prePersist($secondLifecycleEventArgs);
    }

    function it_does_not_generate_duplicated_slugs_based_on_internal_memory_when_updating(
        ObjectManager $objectManager,
        ObjectRepository $productTranslationRepository,
        LifecycleEventArgs $firstLifecycleEventArgs,
        ProductTranslationInterface $firstProductTranslation,
        LifecycleEventArgs $secondLifecycleEventArgs,
        ProductTranslationInterface $secondProductTranslation
    ) {
        $firstLifecycleEventArgs->getObjectManager()->willReturn($objectManager);
        $secondLifecycleEventArgs->getObjectManager()->willReturn($objectManager);

        $objectManager->getRepository(get_class($firstProductTranslation->getWrappedObject()))->willReturn($productTranslationRepository);
        $objectManager->getRepository(get_class($secondProductTranslation->getWrappedObject()))->willReturn($productTranslationRepository);

        $firstLifecycleEventArgs->getObject()->willReturn($firstProductTranslation);
        $secondLifecycleEventArgs->getObject()->willReturn($secondProductTranslation);

        $firstProductTranslation->getName()->willReturn('Banana');
        $secondProductTranslation->getName()->willReturn('Banana');

        $productTranslationRepository->findOneBy(['slug' => 'banana'])->willReturn(null);
        $productTranslationRepository->findOneBy(['slug' => 'banana-1'])->willReturn(null);

        $firstProductTranslation->setSlug('banana')->shouldBeCalled();
        $secondProductTranslation->setSlug('banana-1')->shouldBeCalled();

        $this->preUpdate($firstLifecycleEventArgs);
        $this->preUpdate($secondLifecycleEventArgs);
    }

    function it_does_not_generate_duplicated_slugs_based_on_database_when_inserting(
        LifecycleEventArgs $lifecycleEventArgs,
        ObjectManager $objectManager,
        ProductTranslationInterface $productTranslation,
        ObjectRepository $productTranslationRepository
    ) {
        $lifecycleEventArgs->getObjectManager()->willReturn($objectManager);
        $objectManager->getRepository(get_class($productTranslation->getWrappedObject()))->willReturn($productTranslationRepository);

        $lifecycleEventArgs->getObject()->willReturn($productTranslation);
        $productTranslation->getName()->willReturn('Banana');

        $productTranslationRepository->findOneBy(['slug' => 'banana'])->willReturn(new \stdClass());
        $productTranslationRepository->findOneBy(['slug' => 'banana-1'])->willReturn(null);

        $productTranslation->setSlug('banana-1')->shouldBeCalled();

        $this->prePersist($lifecycleEventArgs);
    }

    function it_does_not_generate_duplicated_slugs_based_on_database_when_updating(
        LifecycleEventArgs $lifecycleEventArgs,
        ObjectManager $objectManager,
        ProductTranslationInterface $productTranslation,
        ObjectRepository $productTranslationRepository
    ) {
        $lifecycleEventArgs->getObjectManager()->willReturn($objectManager);
        $objectManager->getRepository(get_class($productTranslation->getWrappedObject()))->willReturn($productTranslationRepository);

        $lifecycleEventArgs->getObject()->willReturn($productTranslation);
        $productTranslation->getName()->willReturn('Banana');

        $productTranslationRepository->findOneBy(['slug' => 'banana'])->willReturn(new \stdClass());
        $productTranslationRepository->findOneBy(['slug' => 'banana-1'])->willReturn(null);

        $productTranslation->setSlug('banana-1')->shouldBeCalled();

        $this->preUpdate($lifecycleEventArgs);
    }
}
