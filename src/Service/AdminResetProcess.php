<?php

namespace PhpSolution\UserAdminBundle\Service;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Lcobucci\JWT\Token\Plain;
use PhpSolution\JwtBundle\Jwt\JwtManagerAwareTrait;
use PhpSolution\UserAdminBundle\Entity\UserAdminInterface;
use Symfony\Component\Security\Core\Encoder\EncoderFactory;

/**
 * Class AdminResetProcess
 */
class AdminResetProcess
{
    use JwtManagerAwareTrait;

    private const TOKEN_USER_CLAIM = 'user';

    /**
     * @var Registry
     */
    private $doctrine;
    /**
     * @var string
     */
    private $entityClass;
    /**
     * @var string
     */
    private $tokenTypeName;
    /**
     * @var SendResetLinkNotifier
     */
    private $notifier;
    /**
     * @var EncoderFactory
     */
    private $passwordEncoder;

    /**
     * AdminResetProcess constructor.
     *
     * @param Registry              $doctrine
     * @param string                $entityClass
     * @param string                $tokenTypeName
     * @param SendResetLinkNotifier $notifier
     * @param EncoderFactory        $passwordEncoder
     */
    public function __construct(Registry $doctrine, string $entityClass, string $tokenTypeName, SendResetLinkNotifier $notifier, EncoderFactory $passwordEncoder)
    {
        $this->doctrine = $doctrine;
        $this->entityClass = $entityClass;
        $this->tokenTypeName = $tokenTypeName;
        $this->notifier = $notifier;
        $this->passwordEncoder = $passwordEncoder;
    }

    /**
     * @param string $email
     */
    public function sendLinkForResetPassword(string $email): void
    {
        $user = $this->doctrine->getRepository($this->entityClass)->findOneBy(['email' => $email, 'enabled' => true]);
        if (!$user instanceof UserAdminInterface) {
            throw new \InvalidArgumentException('Undefined user with email: ' . $email);
        }
        $token = $this->jwtManager->create($this->tokenTypeName, [self::TOKEN_USER_CLAIM => $user->getId()]);
        $this->notifier->sendResetLink($user, $token->__toString());
    }

    /**
     * @param UserAdminInterface $user
     */
    public function resetPassword(UserAdminInterface $user): void
    {
        $this->encodePassword($user);
        $this->doctrine->getManager()->flush();
    }

    /**
     * @param UserAdminInterface $user
     */
    public function encodePassword(UserAdminInterface $user)
    {
        $plainPassword = $user->getPlainPassword();
        if (!empty($plainPassword)) {
            $salt = $user->createSalt();
            $encodedPassword = $this->passwordEncoder->getEncoder($user)->encodePassword($plainPassword, $salt);
            $user->setSalt($salt);
            $user->setPassword($encodedPassword);
        }
    }

    /**
     * @param string $token
     *
     * @return UserAdminInterface
     */
    public function getUserByToken(string $token): UserAdminInterface
    {
        /* @var $token Plain */
        $token = $this->jwtManager->parse($token, $this->tokenTypeName);
        $userId = $token->claims()->get(self::TOKEN_USER_CLAIM);
        if (empty($userId)) {
            throw new \InvalidArgumentException('Undefined token user');
        }

        return $this->doctrine->getRepository($this->entityClass)->findOneBy(['id' => $userId, 'enabled' => true]);
    }
}