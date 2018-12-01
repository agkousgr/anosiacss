<?php


namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class ConvertGreekFinalS extends AbstractExtension
{
    public function getFilters()
    {
        return array(
            new TwigFilter('preplace', array($this, 'preplace')),
        );
    }

    public function preplace($text, $find, $replace, $limit = -1)
    {
        return preg_replace(
//            sprintf('#%s#u', preg_quote($find, '#')),
            '/' . $find . '$/',
            $replace,
            $text,
            $limit
        );
    }
}