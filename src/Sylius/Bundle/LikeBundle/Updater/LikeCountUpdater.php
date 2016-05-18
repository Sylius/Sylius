<?php
/**
 * Created by PhpStorm.
 * User: loic
 * Date: 18/05/2016
 * Time: 11:54
 */

namespace Sylius\Bundle\LikeBundle\Updater;
use Doctrine\Common\Persistence\ObjectManager;
use Sylius\Component\Like\Model\DislikableInterface;
use Sylius\Component\Like\Model\LikableInterface;
use Sylius\Component\Review\Calculator\LikableLikeCountCalculatorInterface;


/**
 * @author Loïc Frémont <loic@mobizel.com>
 */
class LikeCountUpdater
{
    /**
     * @var LikableLikeCountCalculatorInterface
     */
    private $likableLikeCountCalculator;

    /**
     * @var ObjectManager
     */
    private $likeSubjectManager;

    /**
     * RecalculateLikeCountListener constructor.
     *
     * @param LikableLikeCountCalculatorInterface $likableLikeCountCalculator
     * @param ObjectManager $likeSubjectManager
     */
    public function __construct(LikableLikeCountCalculatorInterface $likableLikeCountCalculator, ObjectManager $likeSubjectManager)
    {
        $this->likableLikeCountCalculator = $likableLikeCountCalculator;
        $this->likeSubjectManager = $likeSubjectManager;
    }

    /**
     * {@inheritdoc}
     */
    public function update(LikableInterface $likeSubject)
    {
        $likeSubject
            ->setLikeCount($this->likableLikeCountCalculator->calculateLikeCount($likeSubject));

        if ($likeSubject instanceof DislikableInterface) {
            $likeSubject
                ->setDislikeCount($this->likableLikeCountCalculator->calculateDislikeCount($likeSubject));
        }

        $this->likeSubjectManager->flush();
    }
}
