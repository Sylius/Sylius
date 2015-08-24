<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\ImportExport\Writer\ORM;

use Doctrine\Common\Persistence\ObjectManager;
use Psr\Log\LoggerInterface;
use Sylius\Bundle\CoreBundle\ImportExport\Processor\UserProcessorInterface;
use Sylius\Component\Core\Model\UserInterface;
use Sylius\Component\ImportExport\Model\JobInterface;
use Sylius\Component\ImportExport\Writer\WriterInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\Component\User\Repository\UserRepositoryInterface;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
class UserWriter implements WriterInterface
{
    /**
     * @var ObjectManager
     */
    private $entityManager;

    /**
     * @var UserRepositoryInterface
     */
    private $userRepository;

    /**
     * @var RepositoryInterface
     */
    private $customerRepository;

    /**
     * @var int
     */
    private $resultCode = 0;

    /**
     * @var UserProcessorInterface
     */
    private $userProcessor;

    /**
     * @param UserProcessorInterface  $userProcessor
     * @param UserRepositoryInterface $userRepository,
     * @param RepositoryInterface     $customerRepository,
     * @param ObjectManager           $entityManager
     */
    public function __construct(
        UserProcessorInterface $userProcessor,
        UserRepositoryInterface $userRepository,
        RepositoryInterface $customerRepository,
        ObjectManager $entityManager
    ) {
        $this->userProcessor = $userProcessor;
        $this->userRepository = $userRepository;
        $this->customerRepository = $customerRepository;
        $this->entityManager = $entityManager;
    }
    /**
     * {@inheritdoc}
     */
    public function write(array $rawUsers, array $configuration, LoggerInterface $logger)
    {
        $rawUsers = $this->userProcessor->revert($rawUsers, $configuration['date_format']);

        foreach ($rawUsers as $newUserData) {
            $user = $this->findOrCreateUser($newUserData['customer']['emailCanonical']);
            $user = $this->updateUser($user, $newUserData);

            $this->entityManager->persist($user);
        }

        $this->entityManager->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function finalize(JobInterface $job, array $configuration)
    {
        $job->addMetadata(array('result_code' => $this->resultCode));
    }

    /**
     * {@inheritdoc}
     */
    public function getResultCode()
    {
        return $this->resultCode;
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return 'user_orm';
    }

    /**
     * @param string $email
     *
     * @return UserInterface
     */
    private function findOrCreateUser($email)
    {
        $user = $this->userRepository->findOneByEmail($email);
        if (null !== $user) {
            return $user;
        }

        $user = $this->userRepository->createNew();
        $customer = $this->customerRepository->findOneBy(array('emailCanonical' => $email));
        if (null === $customer) {
            $customer = $this->customerRepository->createNew();
        }

        $user->setCustomer($customer);

        return $user;
    }

    /**
     * @param UserInterface $user
     * @param array         $newData
     *
     * @return UserInterface
     */
    private function updateUser(UserInterface $user, array $newData)
    {
        $customer = $user->getCustomer();
        foreach ($newData['customer'] as $customerField => $customerValue) {
            $setter = 'set'.ucfirst($customerField);
            $this->setValue($customer, $customerValue, $setter);
        }

        $newData['customer'] = $customer;

        foreach ($newData as $field => $value) {
            $setter = 'set'.ucfirst($field);
            $this->setValue($user, $value, $setter);
        }

        return $user;
    }

    /**
     * @param mixed  $subject
     * @param mixed  $value
     * @param string $setter
     */
    private function setValue($subject, $value, $setter)
    {
        if (method_exists($subject, $setter)) {
            $subject->$setter($value);
        }
    }
}
