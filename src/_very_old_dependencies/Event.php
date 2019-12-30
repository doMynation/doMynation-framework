<?php

use Doctrine\DBAL\Connection;

/**
 * @todo Replace this very old class with the EventDispatcherInterface.
 *
 *
 * @author Dominique Sarrazin <domynation@gmail.com>
 */
final class Event
{

    /**
     * @var Connection
     */
    static $db;

    /**
     * Ugly hack until I get the time to replace this completely with the EventDispatcher.
     *
     * @param \Doctrine\DBAL\Connection $db
     */
    public static function setDatabase(Connection $db)
    {
        static::$db = $db;
    }

    public static function log($eventCode, $eventSourceID, $subjectID, $targetID, $target, $contentType = null)
    {
        if (is_null($contentType)) {
            // @deprecated Attempt to guess the content type. "customer" by default (adapter from old version)
            $categoryCode = static::$db->executeQuery('select ec.code FROM dmn_events as e left join dmn_event_categories ec ON e.category_id = ec.id WHERE e.code = ?', [$eventCode])->fetchColumn();
            $contentType = $categoryCode !== false ? $categoryCode : "customer";
        }

        static::$db->insert('dmn_user_events', [
            'code'         => $eventCode,
            'author_id'    => $eventSourceID,
            'content_type' => $contentType,
            'content_id'   => $subjectID,
            'item_id'      => $targetID,
            'item_name'    => $target,
            'occurred_at'   => (new \DateTime)->format("Y-m-d H:i:s")
        ]);
    }
}
