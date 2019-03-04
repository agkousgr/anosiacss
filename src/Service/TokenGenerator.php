<?php

namespace App\Service;

/**
 * Class TokenGenerator
 */
class TokenGenerator
{
    public function generateToken(int $length = 32)
    {
        if(!isset($length) || intval($length) <= 8 )
            $length = 32;

        return bin2hex(random_bytes($length));
    }
}
