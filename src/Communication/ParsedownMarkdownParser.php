<?php

declare(strict_types=1);

namespace Domynation\Communication;

use Parsedown;

/**
 * Class ParsedownMarkdownParser
 *
 * @package Domynation\Communication
 * @author Dominique Sarrazin <domynation@gmail.com>
 */
final class ParsedownMarkdownParser implements MarkdownParserInterface
{
    private Parsedown $parsedown;

    public function __construct(Parsedown $parsedown)
    {
        $this->parsedown = $parsedown;
    }

    /**
     * {@inheritdoc}
     */
    public function parse(string $text) : string
    {
        return $this->parsedown->text($text);
    }
}