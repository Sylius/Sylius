<?php
/*
* This file is part of the Sylius package.
*
* (c) Paweł Jędrzejewski
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Sylius\Bundle\UserBundle\Form\EventListener;

use Sylius\Component\Resource\Exception\UnexpectedTypeException;
use Sylius\Component\User\Canonicalizer\CanonicalizerInterface;
use Sylius\Component\User\Model\UserInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
class CanonicalizerFormListener implements EventSubscriberInterface
{
    public function __construct(CanonicalizerInterface $canonicalizer)
    {
        $this->canonicalizer = $canonicalizer;
    }

    public static function getSubscribedEvents()
    {
        return array(
            FormEvents::SUBMIT => 'submit',
        );
    }

    public function submit(FormEvent $event)
    {
        $user = $event->getData();

        if (!$user instanceof UserInterface) {
            throw new UnexpectedTypeException($user, 'Sylius\Component\User\Model\UserInterface');
        }
        $user->setUsernameCanonical($this->canonicalizer->canonicalize($user->getUsername()));
        $user->setEmailCanonical($this->canonicalizer->canonicalize($user->getEmail()));
    }
}
