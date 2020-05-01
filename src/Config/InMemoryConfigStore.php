<?php

declare(strict_types=1);

namespace Domynation\Config;

use Domynation\Storage\InMemoryStore;

/**
 * Class InMemoryConfigStore
 *
 * @package Domynation\Config
 * @author Dominique Sarrazin <domynation@gmail.com>
 */
final class InMemoryConfigStore extends InMemoryStore implements ConfigInterface
{
}