<?php

namespace PhpSolution\UserAdminBundle\Entity;

/**
 * Class BasicUserAdmin
 */
class BasicUserAdmin implements UserAdminInterface, \Serializable
{
    const ROLE_ADMIN = 'ROLE_SYSTEM_ADMIN';

    /**
     * @var int
     */
    protected $id;
    /**
     * @var string
     */
    protected $email;
    /**
     * @var string
     */
    protected $password;
    /**
     * @var string
     */
    protected $salt;
    /**
     * @var \DateTime
     */
    protected $dateCreatedAt;
    /**
     * @var bool
     */
    protected $enabled = true;
    /**
     * @var array|\Symfony\Component\Security\Core\Role\Role[]
     */
    protected $roles = [self::ROLE_ADMIN];
    /**
     * @var string
     */
    protected $plainPassword;

    /**
     * BasicUserAdmin constructor.
     */
    public function __construct()
    {
        $this->salt = $this->createSalt();
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return (string) $this->getUsername();
    }

    /**
     * @return int
     */
    public function getId():? int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getEmail():? string
    {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    /**
     * @return string
     */
    public function getPassword():? string
    {
        return $this->password;
    }

    /**
     * @param string $password
     */
    public function setPassword(string $password): void
    {
        $this->password = $password;
    }

    /**
     * @return string
     */
    public function getSalt(): string
    {
        return $this->salt ?: $this->salt = $this->createSalt();
    }

    /**
     * @param string $salt
     */
    public function setSalt(string $salt): void
    {
        $this->salt = $salt;
    }

    /**
     * @return \DateTime
     */
    public function getDateCreatedAt():? \DateTime
    {
        return $this->dateCreatedAt;
    }

    /**
     * @param \DateTime|null $dateCreatedAt
     */
    public function setDateCreatedAt(\DateTime $dateCreatedAt = null): void
    {
        $this->dateCreatedAt = $dateCreatedAt;
    }

    /**
     * Update dateCreatedAt
     */
    public function updateDateCreatedAt(): void
    {
        if (null === $this->dateCreatedAt) {
            $this->dateCreatedAt = new \DateTime();
        }
    }

    /**
     * @return bool
     */
    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    /**
     * @param bool $enabled
     */
    public function setEnabled(bool $enabled): void
    {
        $this->enabled = $enabled;
    }

    /**
     * @return array|\Symfony\Component\Security\Core\Role\Role[]
     */
    public function getRoles(): array
    {
        return $this->roles;
    }

    /**
     * @return string
     */
    public function getPlainPassword():? string
    {
        return $this->plainPassword;
    }

    /**
     * @param string $plainPassword
     */
    public function setPlainPassword(string $plainPassword): void
    {
        $this->plainPassword = $plainPassword;
        if (!empty($plainPassword)) {
            $this->eraseCredentials();
        }
    }

    /**
     * @return string
     */
    public function serialize()
    {
        return serialize(
            [
                $this->getId(),
                $this->getEmail(),
                $this->getPassword(),
                $this->getSalt(),
                $this->getRoles(),
                $this->isEnabled(),
            ]
        );
    }

    /**
     * @param string $serialized
     */
    public function unserialize($serialized): void
    {
        list(
            $this->id,
            $this->email,
            $this->password,
            $this->salt,
            $this->roles,
            $this->enabled
            )
            = unserialize($serialized);
    }

    /**
     * @return string
     */
    public function createSalt(): string
    {
        return base64_encode(random_bytes(30));
    }

    /**
     * @return string
     */
    public function getUsername():? string
    {
        return $this->email;
    }

    /**
     * Removes sensitive data from the user.
     */
    public function eraseCredentials(): void
    {
        $this->salt = null;
        $this->password = null;
    }
}