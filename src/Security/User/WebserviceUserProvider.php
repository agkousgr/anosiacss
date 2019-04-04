<?php
namespace App\Security\User;

use App\Security\User\WebserviceUser;
use App\Service\UserAccountService;
use Psr\Log\LoggerInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;

class WebserviceUserProvider implements UserProviderInterface
{
    /**
     * @var UserAccountService
     */
    protected $userAccountService;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    public function __construct(LoggerInterface $logger, UserAccountService $userAccountService)
    {
        $this->userAccountService = $userAccountService;
        $this->logger = $logger;
    }

    public function loadUserByUsername($username)
    {
        // make a call to your webservice here
        $userData = $this->userAccountService->getUser($username);
        // pretend it returns an array on success, false if there is no user

        if ($userData) {
            $salt = null;
            $roles = array();
            $userData["firstname"] = 'john';
            $userData["lastname"] = 'krav';
            return new WebserviceUser($username, $userData["password"], $userData["firstname"], $userData["lastname"], $salt, $roles);
        }

        throw new UsernameNotFoundException(
            sprintf('Username "%s" does not exist.', $username)
        );
    }

    public function refreshUser(UserInterface $user)
    {
        if (!$user instanceof WebserviceUser) {
            throw new UnsupportedUserException(
                sprintf('Instances of "%s" are not supported.', get_class($user))
            );
        }

        return $this->loadUserByUsername($user->getUsername());
    }

    public function supportsClass($class)
    {
        return WebserviceUser::class === $class;
    }
}