<?php
declare(strict_types=1);

namespace Sylius\Behat\Context\Transform;

use Behat\Behat\Context\Context;
use Sylius\Component\Addressing\Model\PostCodeInterface;
use Sylius\Component\Addressing\Repository\PostCodeRepositoryInterface;
use Webmozart\Assert\Assert;

class PostCodeContext implements Context
{
    /** @var PostCodeRepositoryInterface */
    private $postCodeRepository;

    public function __construct(PostCodeRepositoryInterface $repository)
    {
        $this->postCodeRepository = $repository;
    }

    /**
     * @Transform /^post code "([^"]+)"$/
     * @Transform /^"([^"]+)" post code$/
     */
    public function getPostCodeByCode(string $code): PostCodeInterface
    {
        $postCode = $this->postCodeRepository->findOneBy(['postCode' => $code]);
        Assert::notNull($postCode, sprintf("Post code '%s' was not found by code", $code));

        return $postCode;
    }
}
