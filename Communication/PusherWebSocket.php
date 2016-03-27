<?php

namespace Domynation\Communication;

use Assert\Assertion;
use Pusher;

final class PusherWebSocket implements WebSocketInterface
{
    /**
     * @var \Pusher
     */
    private $pusher;

    public function __construct(string $publicKey, string $privateKey, string $appId)
    {
        $this->pusher = new Pusher($publicKey, $privateKey, $appId, ['encrypted' => true]);
    }

    /**
     * {@inheritdoc}
     */
    public function join(string $channel, array $data = [])
    {
        Assertion::notEmptyKey($data, 'socketId', "Invalid socket id");
        Assertion::notEmptyKey($data, 'userId', "Invalid user id");
        Assertion::notEmptyKey($data, 'fullName', "Invalid full name");

        return $this->pusher->presence_auth($channel, $data['socketId'], $data['userId'], [
            'id'   => $data['userId'],
            'name' => utf8_encode($data['fullName'])
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function send(string $channel, string $event, array $data = [])
    {
        $this->pusher->trigger($channel, $event, $data);
    }
}