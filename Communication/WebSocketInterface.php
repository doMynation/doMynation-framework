<?php

namespace Domynation\Communication;

interface WebSocketInterface
{

    /**
     * Joins a channel. Additional parameters (such as socket id, password, etc) can be
     * provided using the third argument.
     *
     * @param string $channel The channel name
     * @param array $data Additional arguments
     *
     * @return mixed
     */
    public function join(string $channel, array $data = []);

    /**
     * Sends an event to a specific channel. Additional parameters can be provided
     * using the third argument.
     *
     * @param string $channel The name of the channel
     * @param string $event The name of the event
     * @param array $data Additional arguments
     *
     * @return mixed
     */
    public function send(string $channel, string $event, array $data = []);
}