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

use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Yaml\Yaml;

class UserManager extends UserProvider
{
    /**
     * @param User $user
     * @throws UsernameNotFoundException
     */
    public function flushUser(User $user)
    {
        //var_dump('<hr><pre>', $this->users);
        if (!isset($this->users[$user->getUsername()])) {
            throw new UsernameNotFoundException(
                sprintf('U¿ytkownik nie istnieje.')
            );
        }

        unset($this->users[$user->getUsername()]);
        $this->users[$user->getUsername()] = $user;
        file_put_contents($this->ymlPath, Yaml::dump($this->__arrayUsersToYamlUsers($this->users), 2, 2));
        //var_dump('<hr><pre>', Yaml::dump($this->__arrayUsersToYamlUsers($this->users)));
        //die();
    }
}