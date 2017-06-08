<?php

namespace Is\Security\User;

use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Yaml\Yaml;

class UserProvider implements UserProviderInterface
{
    protected $users;

    public function __construct($yml_path)
    {
        $usersFromYaml = Yaml::parse(file_get_contents($yml_path));
        $this->users = array();

        foreach ($usersFromYaml as $username => $attributes) {
            $password = isset($attributes['password']) ? $attributes['password'] : null;
            $salt = isset($attributes['salt']) ? $attributes['salt'] : null;
            $roles = isset($attributes['roles']) ? $attributes['roles'] : array();
            $this->users[$username] = new User($username, $password, $salt, $roles);
        }
    }
    /**
     * Loads the user for the given username.
     *
     * This method must throw UsernameNotFoundException if the user is not
     * found.
     *
     * @param string $username The username
     *
     * @return UserInterface
     *
     * @throws UsernameNotFoundException if the user is not found
     */
    public function loadUserByUsername($username)
    {
        if (!isset($this->users[$username])) {
            throw new UsernameNotFoundException(
                sprintf('U¿ytkownik "%s" nie istnieje.', $username)
            );
        }

        $user = $this->users[$username];

        return new User($user->getUsername(), $user->getPassword(), $user->getSalt(), $user->getRoles());
    }

    /**
     * Refreshes the user for the account interface.
     *
     * It is up to the implementation to decide if the user data should be
     * totally reloaded (e.g. from the database), or if the UserInterface
     * object can just be merged into some internal array of users / identity
     * map.
     *
     * @param UserInterface $user
     *
     * @return UserInterface
     *
     * @throws UnsupportedUserException if the account is not supported
     */
    public function refreshUser(UserInterface $user)
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', get_class($user)));
        }

        return $this->loadUserByUsername($user->getUsername());
    }

    /**
     * Whether this provider supports the given user class.
     *
     * @param string $class
     *
     * @return bool
     */
    public function supportsClass($class)
    {
        return $class === 'Symfony\Component\Security\Core\User\User';
    }
}