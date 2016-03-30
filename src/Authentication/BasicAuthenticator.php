<?php

namespace Domynation\Authentication;

use Doctrine\DBAL\Connection;
use Domynation\Exceptions\AuthenticationException;
use Domynation\Security\PasswordInterface;
use Domynation\Session\SessionInterface;

final class BasicAuthenticator implements AuthenticatorInterface
{

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
     * @var UserInterface
     */
    private $user;

    public function __construct(Connection $db, SessionInterface $session, PasswordInterface $passwordManager)
    {
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
        // Fetch the user
        $user = $this->db->createQueryBuilder()
            ->select('user.id, user.password')
            ->from('users', 'user')
            ->andWhere('user.username = :username')
            ->setParameter('username', $username)
            ->execute()->fetch();

        if ($user === false) {
            return false;
        }

        // Check if the password matches
        if (!$this->passwordManager->check($password, $user['password'])) {
            return false;
        }

        return $user['id'];
    }

    /**
     * {@inheritdoc}
     */
    public function authenticate($userId)
    {
        // Fetch the user
        $userInfo = $this->db->fetchAssoc('SELECT * FROM users WHERE id = ?', [$userId]);

        if ($userInfo === false) {
            throw new AuthenticationException("Authentication failed");
        }

        // Create the user instance
        $user = $this->hydrateUser($userInfo);

        // Generate session fingerprint
        $sessionFingerprint = $this->createSessionFingerprint();

        // Update the user's info in the database
        $now = (new \DateTime)->format("Y-m-d H:i:s");
        $this->db->update('users', [
            'ip_address'         => $_SERVER['REMOTE_ADDR'],
            'is_online'          => 1,
            'ss_fprint'          => $sessionFingerprint,
            'last_login_date'    => $now,
            'last_activity_date' => $now
        ], ['id' => $userId]);

        // Set session
        $this->session->set('ID', $userId);
        $this->session->set('ss_fprint', $sessionFingerprint);

        return $user;
    }

    /**
     * Hydrates an associative array containing the user info into
     * a User object.
     *
     * @param array $user
     *
     * @return \Domynation\Authentication\User
     */
    private function hydrateUser(array $user)
    {
        return new User($user['id'], $user['username'], $user['full_name']);
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
        $userInfo = $this->db->fetchAssoc('SELECT * FROM users WHERE is_online = 1 AND id = ?', [$userId]);

        if (empty($userInfo)) {
            return null;
        }

        // Store the user
        $this->user = $this->hydrateUser($userInfo);

        return $this->user;
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
}