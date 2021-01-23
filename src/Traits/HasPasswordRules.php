<?php


namespace Golly\Authority\Traits;


use Golly\Authority\Rules\PasswordRule;

/**
 * Trait HasPasswordRules
 * @package Golly\Authority\Traits
 */
trait HasPasswordRules
{
    /**
     * Get the validation rules used to validate passwords.
     *
     * @return array
     */
    protected function getPasswordRules()
    {
        $password = new PasswordRule();

        return [
            'required',
            'string',
            'confirmed',
            $password->requireNumeric()
        ];
    }
}
