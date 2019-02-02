<?php


namespace App\Service;


class TransliterateService
{
    /**
     * @var array
     */
    private $txtPatterns;

    /**
     * @var array
     */
    private $txtReplacements;

    /**
     * @var array
     */
    private $_urlPatterns;

    /**
     * @var array
     */
    private $_urlReplacements;

    public function __construct()
    {
        $this->txtPatterns = array(
            '/(μπ)/u', '/(ντ)/u', '/(τζ)/u', '/(γκ)/u', '/(Μπ)/u', '/(Ντ)/u', '/(Τζ)/u', '/(Γκ)/u',
            '/(Oι|Οί|Ει|Εί|[ΙΊΗΉ]|Υι|Υί)/u', '/(Αι|Αί|[ΕΈ])/u',
            '/(oι|oί|ει|εί|[ιΐϊίηή]|υι|υί)/u', '/(αι|αί|[εέ])/u', '/(ου|ού)/u',
            '/(Ου|Ού)/u', '/[όο]/u', '/[ωώ]/u', '/[αά]/u', '/β/u', '/γ/u', '/δ/u', '/ζ/u', '/θ/u',
            '/κ/u', '/(λλ)/u', '/λ/u', '/μ/u', '/ν/u', '/ξ/u', '/π/u', '/ρ/u', '/[σς]/u', '/τ/u',
            '/[υΰϋύ]/u', '/φ/u', '/χ/u', '/ψ/u', '/[ΌΟ]/u', '/[ΩΏ]/u', '/[ΑΆ]/u', '/Β/u', '/Γ/u', '/Δ/u',
            '/Z/u', '/Θ/u', '/Κ/u', '/Λ/u', '/Μ/u', '/Ν/u', '/Ξ/u', '/Π/u', '/Ρ/u',
            '/Σ/u', '/Τ/u', '/[ΥΎΫ]/u', '/Φ/u', '/Χ/u', '/Ψ/u');
        $this->txtReplacements = array(
            'b', 'd', 'j', 'g', 'B', 'D', 'J', 'G', 'I', 'E', 'i', 'e', 'ou',
            'Ou', 'o', 'w', 'a', 'v', 'g', 'd', 'z', 'th', 'k', 'l', 'l', 'm', 'n',
            'x', 'p', 'r', 's', 't', 'y', 'f', 'h', 'ps', 'O', 'W', 'A', 'V', 'G',
            'D', 'Z', 'Th', 'K', 'L', 'M', 'N', 'X', 'P', 'R', 'S', 'T', 'Y',
            'F', 'H', 'Ps');
        $this->_urlPatterns = array(
            '/(μπ)/u', '/(τζ)/u', '/(γκ)/u', '/(Μπ)/u', '/(Τζ)/u', '/(Γκ)/u',
            '/(Oι|Οί|Ει|Εί|[ΙΊΗΥΎΉ]|Υι|Υί)/u', '/(Αι|Αί|[ΕΈ])/u',
            '/(oι|oί|ει|εί|[ιΐϊΰϋίυύηή]|υι|υί)/u', '/(αι|αί|[εέ])/u', '/(ου|ού)/u',
            '/(Ου|Ού)/u', '/[ωώόο]/u', '/[αά]/u', '/β/u', '/γ/u', '/δ/u', '/ζ/u', '/θ/u',
            '/κ/u', '/(λλ)/u', '/λ/u', '/μ/u', '/ν/u', '/ξ/u', '/π/u', '/ρ/u', '/[σς]/u', '/τ/u',
            '/φ/u', '/χ/u', '/ψ/u', '/[ΩΏΌΟ]/u', '/[ΑΆ]/u', '/Β/u', '/Γ/u', '/Δ/u',
            '/Z/u', '/Θ/u', '/Κ/u', '/Λ/u', '/Μ/u', '/Ν/u', '/Ξ/u', '/Π/u', '/Ρ/u',
            '/Σ/u', '/Τ/u', '/Φ/u', '/Χ/u', '/Ψ/u');
        $this->_urlReplacements = array(
            'b', 'j', 'g', 'B', 'J', 'G', 'I', 'E', 'i', 'e', 'ou',
            'Ou', 'o', 'a', 'v', 'g', 'd', 'z', 'th', 'k', 'l', 'l', 'm', 'n',
            'x', 'p', 'r', 's', 't', 'f', 'h', 'ps', 'O', 'A', 'V', 'G',
            'D', 'Z', 'Th', 'K', 'L', 'M', 'N', 'X', 'P', 'R', 'S', 'T',
            'F', 'H', 'Ps');
    }

    public function transGreek($text, $method = "txt")
    {
        return $this->transliterate($text, $method);
    }

    private function transliterate($string, $method = 'txt')
    {
        switch ($method) {
            case 'url':
                return preg_replace($this->_urlPatterns, $this->_urlReplacements, $string);
                break;
            case 'txt':
            default:
                return preg_replace($this->txtPatterns, $this->txtReplacements, $string);
                break;
        }
    }
}