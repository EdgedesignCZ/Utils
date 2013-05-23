<?php

namespace Edge\Utils\Generator;

/**
 * Password generator
 *
 * @author VeN <vaclav.novotny@edgedesign.cz>
 */
class PasswordGenerator
{

    /**
     * Default length of password
     */
    const DEFAULT_LENGTH = 30;

    /**
     * Generate random password
     *
     * @return string
     */
    public function generatePassword($length = self::DEFAULT_LENGTH)
    {
        // Simple solution from http://stackoverflow.com/questions/6101956/generating-a-random-password-in-php
        // + some hard-to-read characters were removed
        return substr(str_shuffle(
            'aaabbbbccccddddeeeeffffgggghhhhjjjjkkkkmmmmnnnnoooopppprrrrsssstttt'
            . 'uuuuvvvvwwwwxxxxAAAABBBBCCCCDDDDEEEEFFFFGGGGHHHHJJJJKKKKLLLLMMMM'
            . 'NNNNPPPPQQQQRRRRSSSSTTTTUUUUVVVVWWWWXXXX222233334444555566667777'
            . '88889999'
        ), 0, $length);
    }

}
