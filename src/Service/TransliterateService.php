<?php

namespace App\Service;

/**
 * Class TransliterateService
 */
class TransliterateService
{
    public function transliterateToGreek(string $subject): string
    {
        $trans = \Transliterator::create('Any-Greek');

        return $trans->transliterate($subject);
    }
}