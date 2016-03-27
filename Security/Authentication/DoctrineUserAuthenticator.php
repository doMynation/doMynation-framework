<?php

namespace Domynation\Security\Authentication;

use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManager;
use Domynation\Security\PasswordInterface;
use Domynation\Session\SessionInterface;
use Solarius\Common\Entities\Permission;
use Solarius\Common\Entities\User;

final class DoctrineUserAuthenticator implements UserAuthenticatorInterface
{

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    /**
     * @var \Doctrine\DBAL\Connection
     */
    private $db;

    /**
     * @var \Domynation\Session\SessionInterface
     */
    private $session;

    /**
     * @var \Domynation\Security\PasswordInterface
     */
    private $passwordManager;

    /**
     * @var User
     */
    private $user;

    public function __construct(EntityManager $em, Connection $db, SessionInterface $session, PasswordInterface $passwordManager)
    {
        $this->em              = $em;
        $this->db              = $db;
        $this->session         = $session;
        $this->passwordManager = $passwordManager;

        $this->user = null;
    }

    /**
     * {@inheritdoc}
     */
    public function attempt($username, $password)
    {
        $user = $this->em->createQueryBuilder()
            ->select('user')
            ->from(User::class, 'user')
            ->where('user.disabledAt IS NULL')
            ->andWhere('user.username = :username')
            ->setParameter('username', $username)
            ->getQuery()
            ->getOneOrNullResult();

        if ($user === null) {
            return false;
        }

        if (!$this->passwordManager->check($password, $user->getPassword())) {
            return false;
        }

        return $user->getId();
    }

    /**
     * {@inheritdoc}
     */
    public function authenticate($userId)
    {
        $user = $this->em->getRepository(User::class)->find($userId);

        if ($user === false) {
            return false;
        }

        // Generate session fingerprint
        $sessionFingerprint = $this->createSessionFingerprint();
        $now                = (new \DateTime)->format("Y-m-d H:i:s");

        // Update the user session
        $this->db->update('users', [
            'ip_address'         => $_SERVER['REMOTE_ADDR'],
            'is_online'          => 1,
            'ss_fprint'          => $sessionFingerprint,
            'last_login_date'    => $now,
            'last_activity_date' => $now
        ], ['id' => $userId]);

        // Log connection
        $this->db->insert('connection_logs', [
            'user_id'            => $userId,
            'user_ip_address'    => $_SERVER['REMOTE_ADDR'],
            'session_start_date' => $now
        ]);

        // Set session
        $this->session->set('ID', $userId);
        $this->session->set('ss_fprint', $sessionFingerprint);

        // Set the authenticated user
        $this->user = $user;

        $this->loadBackwardCompatibleLogic($user);

        return $user;
    }

    /**
     * {@inheritdoc}
     */
    public function remember()
    {
        $userId = $this->session->get('ID');

        if (is_null($userId) || !is_numeric($userId) || !$this->checkSession()) {
            return;
        }

        // Fetch the user info
        $user = $this->em->getRepository(User::class)->findOneBy(['id' => $userId, 'isOnline' => 1]);

        if ($user === false) {
            return null;
        }

        // Store the user
        $this->user = $user;

        $this->loadBackwardCompatibleLogic($user);

        return $user;
    }

    /**
     * {@inheritdoc}
     */
    public function deauthenticate()
    {
        $this->db->update('users', [
            'is_online' => false
        ], ['id' => $this->session->get('ID')]);

        $this->session->remove('ID');

        $this->user = null;
    }

    /**
     * {@inheritdoc}
     */
    public function isAuthenticated()
    {
        return !is_null($this->user);
    }

    /**
     * {@inheritdoc}
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Generates a session fingerprint.
     *
     * @return string
     */
    private function createSessionFingerprint()
    {
        $ipBlocks = explode('.', $_SERVER['REMOTE_ADDR']);

        // Generate a fingerprint based on a random prefix, the user agent, and a couple of IP blocks
        $fingerprint = "dmn-{$_SERVER['HTTP_USER_AGENT']}-{$ipBlocks[0]}:{$ipBlocks[1]}:{$ipBlocks[2]}";

        // Hash the result
        return md5($fingerprint);
    }

    /**
     * Checks if the session fingerprint is correct.
     *
     * @return bool
     */
    private function checkSession()
    {
        return $this->session->has('ss_fprint') && $this->createSessionFingerprint() === $this->session->get('ss_fprint');
    }

    /**
     * @param \Solarius\Common\Entities\User $user
     */
    private function loadBackwardCompatibleLogic(User $user)
    {
        // Transform user permissions
        $permissions = array_map(function (Permission $permission) {
            return $permission->getCode();
        }, $user->getPermissions());

        $userData = array_merge($user->toArray(), ['permissions' => $permissions]);

        // Backward compatibility hook
        \User::init($userData);
    }
}