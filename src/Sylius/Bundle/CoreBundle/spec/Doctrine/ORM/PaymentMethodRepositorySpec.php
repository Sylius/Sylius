<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\CoreBundle\Doctrine\ORM;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\QueryBuilder;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Payment\Model\PaymentMethodInterface;
use Sylius\Component\Payment\Repository\PaymentMethodRepositoryInterface;

class PaymentMethodRepositorySpec extends ObjectBehavior
{
    public function let(EntityManager $em, ClassMetadata $classMetadata)
    {
        $this->beConstructedWith($em, $classMetadata);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\CoreBundle\Doctrine\ORM\PaymentMethodRepository');
    }

    function it_is_a_repository()
    {
        $this->shouldHaveType(EntityRepository::class);
        $this->shouldImplement(PaymentMethodRepositoryInterface::class);
    }

    function it_creates_query_builder_for_the_payment_method(
        $em,
        QueryBuilder $builder,
        ChannelInterface $channel,
        ArrayCollection $paymentMethods,
        PaymentMethodInterface $paymentMethod
    ) {
        $em->createQueryBuilder()->shouldBeCalled()->willReturn($builder);
        $builder->select('method')->shouldBeCalled()->willReturn($builder);
        $builder->addSelect('translation')->shouldBeCalled()->willReturn($builder);
        $builder->leftJoin('method.translations', 'translation')->shouldBeCalled()->willReturn($builder);
        $builder->from(Argument::any(), 'method', Argument::cetera())->shouldBeCalled()->willReturn($builder);
        $builder->andWhere('method IN (:methods)')->shouldBeCalled()->willReturn($builder);

        $channel->getPaymentMethods()->shouldBeCalled()->willReturn($paymentMethods);
        $paymentMethods->toArray()->shouldBeCalled()->willReturn([$paymentMethod]);
        $builder->setParameter('methods', [$paymentMethod])->shouldBeCalled()->willReturn($builder);

        $this->getQueryBuilderForChoiceType([
            'channel' => $channel,
            'disabled' => true,
        ])->shouldReturn($builder);
    }
}
