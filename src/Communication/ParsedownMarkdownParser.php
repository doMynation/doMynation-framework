<?php

namespace Domynation\Communication;

use Parsedown;

final class ParsedownMarkdownParser implements MarkdownParserInterface
{
    /**
     * @var \Parsedown
     */
    private $parsedown;

    public function __construct(Parsedown $parsedown)
    {
        $this->parsedown = $parsedown;
    }

    public function parse(string $text) : string
    {
        return $this->parsedown->text($text);
    }
}