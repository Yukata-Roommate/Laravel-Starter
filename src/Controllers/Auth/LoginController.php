<?php

namespace YukataRm\Laravel\Starter\Controllers\Auth;

use YukataRm\Laravel\Auth\Controllers\LoginController as BaseController;

/**
 * Auth Login Controller
 *
 * @package YukataRm\Laravel\Starter\Controllers\Auth
 */
class LoginController extends BaseController
{
    /*----------------------------------------*
     * Attempt
     *----------------------------------------*/

    /**
     * get credentials
     *
     * @return array<string, mixed>
     */
    #[\Override]
    protected function credentials(): array
    {
        return array_merge(
            parent::credentials(),
            [
                "is_active" => 1,
            ]
        );
    }
}
