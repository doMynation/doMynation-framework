<?php

namespace Domynation\Utils;

trait MessageRecorderTrait
{

    /**
     * The collection of messages
     *
     * @var array
     */
    protected $messages = [];

    /**
     * Adds a message.
     *
     * @param string $message
     * @param string $namespace
     */
    public function addMessage($message, $namespace = null)
    {
        if (!is_null($namespace)) {
            $this->addNamespacedMessage($message, $namespace);

            return;
        }

        $this->messages[] = $message;
    }

    /**
     * Adds a message under a namespace.
     *
     * @param string $message
     * @param string $namespace
     */
    private function addNamespacedMessage($message, $namespace)
    {
        $this->messages[$namespace][] = $message;
    }

    /**
     * Adds multiple messages.
     *
     * @param string[] $messages
     * @param string $namespace
     */
    public function addMessages(array $messages, $namespace = null)
    {
        foreach ($messages as $message) {
            $this->addMessage($message, $namespace);
        }
    }

    /**
     * @return bool
     */
    public function hasMessages()
    {
        return !empty($this->messages);
    }

    /**
     * @return array
     */
    public function getMessages()
    {
        return $this->messages;
    }
}