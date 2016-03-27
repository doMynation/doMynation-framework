<?php

namespace Domynation\Communication;

interface MarkdownParserInterface
{

    /**
     * Parses the text.
     *
     * @param string $text
     *
     * @return string
     */
    public function parse(string $text) : string;
}