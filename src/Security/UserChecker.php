<?php

namespace App\Security;

use App\Entity\User;
use Symfony\Component\Security\Core\User\{UserCheckerInterface, UserInterface};
use App\Exception\UserInactiveException;

/**
 * Class UserChecker
 */
class UserChecker implements UserCheckerInterface
{
    /**
     * {@inheritdoc}
     */
    public function checkPreAuth(UserInterface $user)
    {
        if (!$user instanceof User)
            return;

        if (!$user->getIsActive())
            throw new UserInactiveException('User is inactive.');
    }

    /**
     * {@inheritdoc}
     */
    public function checkPostAuth(UserInterface $user)
    {
        return;
    }
}
