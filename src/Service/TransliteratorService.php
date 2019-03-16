<?php

namespace App\Service;

/**
 * Class TransliteratorService
 */
class TransliteratorService
{
    public function transliterateToGreek(string $subject): string
    {
        $trans = \Transliterator::create('Any-Greek');

        return $trans->transliterate($subject);
    }
}