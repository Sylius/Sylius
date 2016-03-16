<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ArchetypeBundle\Form\EventListener;

use Sylius\Component\Archetype\Model\ArchetypeInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\Exception\UnexpectedTypeException;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

/**
 * @author Magdalena Banasiak <magdalena.banasiak@lakion.com>
 */
class ParentArchetypeListener implements EventSubscriberInterface
{
    /**
     * @var string
     */
    private $subject;

    /**
     * @param string $subject
     */
    public function __construct($subject)
    {
        $this->subject = $subject;
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [FormEvents::PRE_SET_DATA => 'preSetData'];
    }

    /**
     * @param FormEvent $event
     */
    public function preSetData(FormEvent $event)
    {
        $currentArchetype = $event->getData();
        if (!$currentArchetype instanceof ArchetypeInterface) {
            throw new UnexpectedTypeException($currentArchetype, ArchetypeInterface::class);
        }

        $form = $event->getForm();
        $parentOptions = [
            'required' => false,
            'label' => 'sylius.form.archetype.parent',
            'property' => 'name',
        ];

        if (null != $currentArchetype->getId()) {
            $parentOptions['query_builder'] = function (RepositoryInterface $repository) use ($currentArchetype) {
                return $repository
                    ->createQueryBuilder('o')
                    ->where('o.id != :id')
                    ->setParameter('id', $currentArchetype->getId())
                ;
            };
        }

        $form->add('parent', sprintf('sylius_%s_archetype_choice', $this->subject), $parentOptions);
    }
}
