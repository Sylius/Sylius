<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\ApiBundle\CommandHandler;

use Sylius\Bundle\AdminApiBundle\Model\UserInterface;
use Sylius\Bundle\ApiBundle\Command\AddProductReview;
use Sylius\Bundle\ApiBundle\Context\UserContextInterface;
use Sylius\Bundle\ApiBundle\Provider\CustomerProviderInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\Component\Core\Repository\ProductRepositoryInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\Component\Review\Model\ReviewInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

/** @experimental */
final class AddProductReviewHandler implements MessageHandlerInterface
{
    /** @var UserContextInterface */
    private $userContext;

    /** @var CustomerProviderInterface */
    private $customerProvider;

    /** @var RepositoryInterface */
    private $productReviewRepository;

    /** @var FactoryInterface */
    private $productReviewFactory;

    /** @var ProductRepositoryInterface */
    private $productRepository;

    public function __construct(
        UserContextInterface $userContext,
        CustomerProviderInterface $customerProvider,
        RepositoryInterface $productReviewRepository,
        FactoryInterface $productReviewFactory,
        ProductRepositoryInterface $productRepository
    ) {
        $this->userContext = $userContext;
        $this->customerProvider = $customerProvider;
        $this->productReviewRepository = $productReviewRepository;
        $this->productReviewFactory = $productReviewFactory;
        $this->productRepository = $productRepository;
    }

    public function __invoke(AddProductReview $addProductReview): ReviewInterface
    {
        /** @var ProductInterface $product */
        $product = $this->productRepository->findOneByCode($addProductReview->productCode);

        /** @var CustomerInterface|null $customer */
        $customer = $this->getCustomer();

        /** @var string|null $email */
        $email = $addProductReview->email;

        if ($customer === null && $email === null) {
            throw new \InvalidArgumentException('Visitor should provide an email');
        }

        if ($customer === null && $email !== null) {
            /** @var CustomerInterface $customer */
            $customer = $this->customerProvider->provide($email);
        }

        /** @var ReviewInterface $review */
        $review = $this->productReviewFactory->createNew();
        $review->setTitle($addProductReview->title);
        $review->setRating($addProductReview->rating);
        $review->setComment($addProductReview->comment);
        $review->setReviewSubject($product);
        $review->setAuthor($customer);

        $this->productReviewRepository->add($review);

        $product->addReview($review);

        return $review;
    }

    private function getCustomer(): ?CustomerInterface
    {
        /** @var UserInterface|null $user */
        $user = $this->userContext->getUser();
        if ($user !== null && $user instanceof ShopUserInterface) {
            return $user->getCustomer();
        }

        return null;
    }
}
