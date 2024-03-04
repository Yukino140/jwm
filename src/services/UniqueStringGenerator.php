<?php
// src/Service/UniqueStringGenerator.php

namespace App\services;

class UniqueStringGenerator
{
    private $generatedStrings = [];

    public function generateUniqueString(int $length = 20): string
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $uniqueString = '';

        do {
            for ($i = 0; $i < $length; $i++) {
                $uniqueString .= $characters[random_int(0, $charactersLength - 1)];
            }
        } while (in_array($uniqueString, $this->generatedStrings));

        // Remember the generated string to ensure uniqueness next time.
        $this->generatedStrings[] = $uniqueString;

        return $uniqueString;
    }
}
