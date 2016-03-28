<?php

use Doctrine\DBAL\Connection;

/**
 * @todo: Replace this very old class with the EventDispatcherInterface.
 */
final class Event
{

    /**
     * @var Connection
     */
    static $db;

    static $raisedEvents = [];
    static $listeners = [];

    /**
     * Ugly hack until I get the time to replace this completely with the EventDispatcher.
     *
     * @param \Doctrine\DBAL\Connection $db
     */
    public static function setDatabase(Connection $db)
    {
        static::$db = $db;
    }

    /**
     * Raises an event to be dispatched later
     *
     * @param $eventName
     * @param $data
     */
    public static function raise($eventName, $data)
    {
        static::$raisedEvents[] = [
            'name' => $eventName,
            'data' => $data
        ];
    }

    /**
     * Dispatches all raised events
     */
    public static function dispatch()
    {
        foreach (static::$raisedEvents as $raisedEvent) {
            static::fire($raisedEvent['name'], $raisedEvent['data']);
        }

        // Clear the list
        static::$raisedEvents = [];
    }

    public static function listen($eventCode, callable $closure)
    {
        static::$listeners[] = [
            'event'   => $eventCode,
            'closure' => $closure
        ];
    }

    public static function fire($eventCode, $eventArgs)
    {
        foreach (static::$listeners as $listener) {
            if ($listener['event'] === $eventCode) {
                call_user_func_array($listener['closure'], $eventArgs);
            }
        }
    }

    public static function log($eventCode, $eventSourceID, $subjectID, $targetID, $target, $contentType = null)
    {
        if (is_null($contentType)) {
            // @deprecated Attempt to guess the content type. "customer" by default (adapter from old version)
            $categoryCode = static::$db->executeQuery('select ec.code FROM events as e left join event_categories ec ON e.category_id = ec.id WHERE e.code = ?', [$eventCode])->fetchColumn();
            $contentType  = $categoryCode !== false ? $categoryCode : "customer";
        }

        static::$db->insert('user_activities', [
            'code'         => $eventCode,
            'author_id'    => $eventSourceID,
            'content_type' => $contentType,
            'content_id'   => $subjectID,
            'item_id'      => $targetID,
            'item_name'    => $target,
            'occured_at'   => (new \DateTime)->format("Y-m-d H:i:s")
        ]);
    }
}
