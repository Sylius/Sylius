<?php
/**
 * Created by PhpStorm.
 * User: loic
 * Date: 17/05/2016
 * Time: 16:18
 */

namespace Sylius\Bundle\LikeBundle\EventListener;

use Sylius\Bundle\LikeBundle\Updater\LikeCountUpdaterInterface;
use Sylius\Component\Like\Model\LikeInterface;
use Sylius\Component\Resource\Exception\UnexpectedTypeException;
use Symfony\Component\EventDispatcher\GenericEvent;

/**
 * @author Loïc Frémont <loic@mobizel.com>
 */
class RecalculateLikeCountListener
{
    /**
     * @var LikeCountUpdaterInterface
     */
    private $likeCountUpdater;

    /**
     * @param GenericEvent $event
     */
    public function recalculateLikeCount(GenericEvent $event)
    {
        $like = $event->getSubject();
        if (!$like instanceof LikeInterface) {
            throw new UnexpectedTypeException($like, LikeInterface::class);
        }

        $this->likeCountUpdater->update($like->getLikeSubject());
    }
}
