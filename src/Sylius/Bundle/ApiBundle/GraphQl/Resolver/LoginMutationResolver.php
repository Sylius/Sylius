<?php


namespace Sylius\Bundle\ApiBundle\GraphQl\Resolver;


use ApiPlatform\Core\GraphQl\Resolver\MutationResolverInterface;
use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Sylius\Component\Core\Model\ShopUser;
use Sylius\Component\Core\Model\ShopUserInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;

class LoginMutationResolver implements MutationResolverInterface
{

    /** @var EncoderFactoryInterface */
    private $encoderFactory;

    /** @var EntityManagerInterface */
    private $entityManager;

    /** @var JWTTokenManagerInterface */
    private $jwtManager;

    public function __construct(
        EntityManagerInterface $entityManager,
        JWTTokenManagerInterface $jwtManager,
        EncoderFactoryInterface $encoderFactory
    )
    {
        $this->entityManager = $entityManager;
        $this->jwtManager = $jwtManager;
        $this->encoderFactory = $encoderFactory;
    }

    public function __invoke( $item, $context)
    {
        if(!is_array($context) || !isset($context['args']['input'])){
            return null;
        }

        $username = $context['args']['input']['username'];
        $password = $context['args']['input']['password'];

        $shopUserRepository = $this->entityManager->getRepository(ShopUser::class);

        /** @var ShopUserInterface $user */
        $user = $shopUserRepository->findOneBy(['username' => $username]);

        $encoder = $this->encoderFactory->getEncoder($user);

        if ($encoder->isPasswordValid($user->getPassword(),$password,$user->getSalt())) {
            $token = $this->jwtManager->create($user);
            $user->setToken($token);
            return $user;

        }
        return null;
    }


}
