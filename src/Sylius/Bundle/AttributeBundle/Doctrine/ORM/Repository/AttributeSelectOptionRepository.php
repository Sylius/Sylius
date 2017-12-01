<?php
/**
 *
 *
 * @author Asier Marqués <asiermarques@gmail.com>
 */

namespace Sylius\Bundle\AttributeBundle\Doctrine\ORM\Repository;



use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Sylius\Component\Attribute\Model\AttributeInterface;
use Sylius\Component\Attribute\Repository\AttributeSelectOptionRepositoryInterface;


/**
 * @author Asier Marqués <asier@simettric.com>
 */
class AttributeSelectOptionRepository extends EntityRepository implements AttributeSelectOptionRepositoryInterface
{

    public function getAttributeSelectOptionsQB (AttributeInterface $attribute)
    {

        return $this->createQueryBuilder('o')
            ->innerJoin('o.attribute', 'a')
            ->where('a.id = ' . $attribute->getId());
    }
}