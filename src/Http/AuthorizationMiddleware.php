<?php

namespace Domynation\Http;

use Domynation\Authentication\UserInterface;
use Domynation\Exceptions\AuthorizationException;

final class AuthorizationMiddleware extends RouterMiddleware
{

    /**
     * @var UserInterface
     */
    private $user;

    public function __construct(UserInterface $user)
    {
        $this->user = $user;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(Route $route, array $inputs)
    {
        $permissions = $route->getRequiredPermissions();

        if (!empty($permissions)) {
            if (!$this->user->hasPermission($permissions)) {
                throw new AuthorizationException;
            }
        }

        // Pass the request to the next middleware
        return !is_null($this->next) ? $this->next->handle($route, $inputs) : null;
    }
}