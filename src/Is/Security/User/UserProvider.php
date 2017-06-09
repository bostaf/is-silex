<?php
/**
 * This file is part of the Is package
 *
 * (c) Grzegorz Szaliñski <grzegorz.szalinski@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Is\Security\User;

use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Yaml\Yaml;

class UserProvider implements UserProviderInterface
{
    protected $users;
    protected $ymlPath;

    public function __construct($ymlPath)
    {
        $this->ymlPath = $ymlPath;
        $usersFromYaml = Yaml::parse(file_get_contents($ymlPath));
        $this->users = array();

        $this->users = $this->__yamlUsersToArrayUsers($usersFromYaml);
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

    /**
     * @param array $yamlParseUsersResult
     * @return array
     */
    protected function __yamlUsersToArrayUsers($yamlParseUsersResult)
    {
        $users = array();
        foreach ($yamlParseUsersResult as $username => $attributes) {
            $password = isset($attributes['password']) ? $attributes['password'] : null;
            $salt = isset($attributes['salt']) ? $attributes['salt'] : null;
            $roles = isset($attributes['roles']) ? $attributes['roles'] : array();
            $users[$username] = new User($username, $password, $salt, $roles);
        }
        return $users;
    }

    /**
     * @param array $arrayUsers
     * @return array
     */
    protected function __arrayUsersToYamlUsers($arrayUsers)
    {
        $users = array();
        /**
         * @var $user \Is\Security\User\User;
         */
        $user = null;
        foreach($arrayUsers as $username => $user) {
            $users[$username]['password'] = $user->getPassword();
            $users[$username]['salt'] = $user->getSalt();
            $users[$username]['roles'] = $user->getRoles();
        }
        return $users;
    }
}
