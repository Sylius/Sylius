<?php
/**
 * Created by PhpStorm.
 * User: mamazu
 * Date: 06/02/18
 * Time: 11:56
 */

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
     *
     * @param string $code
     *
     * @return PostCodeInterface
     */
    public function getPostCodeByCode(string $code): PostCodeInterface
    {
        $postCode = $this->postCodeRepository->findOneBy(['postCode' => $code]);
        Assert::notNull($postCode, "Post code '$code' was not found by code");

        return $postCode;
    }
}