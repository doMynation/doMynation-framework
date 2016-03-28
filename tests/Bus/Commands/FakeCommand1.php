<?php

namespace Test\Unit\Framework\Bus\Commands;

use Domynation\Bus\Command;

final class FakeCommand1 extends Command
{
    /**
     * @var int
     */
    protected $id;

    public function __construct($id)
    {
        $this->id = (int)$id;
    }
}