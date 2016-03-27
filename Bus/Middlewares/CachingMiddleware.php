<?php

namespace Domynation\Bus\Middlewares;

use Domynation\Bus\CachableCommand;
use Domynation\Bus\Command;
use Domynation\Bus\CommandHandler;
use Domynation\Cache\CacheInterface;

final class CachingMiddleware extends CommandBusMiddleware
{
    /**
     * @var \Domynation\Cache\CacheInterface
     */
    private $cache;

    public function __construct(CacheInterface $cache)
    {
        $this->cache = $cache;
    }

    public function handle(Command $command, CommandHandler $handler)
    {
        // Attempt to find a cached version of the response
        if ($command instanceof CachableCommand) {
            $key = $command->getCacheName();

            // Attempt to retrieve the item from the cache
            $item = $this->cache->get($key);

            if (!is_null($item)) {
                return $item;
            }

            // Let the other middleware handle the command
            $result = $this->next->handle($command, $handler);

            // Save the result in the cache
            $this->cache->set($key, $result, 60);

            return $result;
        }

        // Pass the request to the next handler
        return $this->next->handle($command, $handler);
    }
}