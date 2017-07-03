<?php

namespace PhpSolution\UserAdminBundle\Entity;

use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Interface UserAdminInterface
 */
interface UserAdminInterface extends UserInterface
{
    /**
     * @return mixed
     */
    public function getId();

    /**
     * @return string
     */
    public function getEmail():? string;

    /**
     * @return string
     */
    public function createSalt(): string;

    /**
     * @param string $salt
     */
    public function setSalt(string $salt);

    /**
     * @param string $password
     */
    public function setPassword(string $password);

    /**
     * @return null|string
     */
    public function getPlainPassword():? string;

    /**
     * @param string $plainPassword
     */
    public function setPlainPassword(string $plainPassword);
}