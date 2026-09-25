<?php

namespace Tests\Feature;

use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * Root sekarang berada di balik autentikasi; tamu diarahkan ke login.
     */
    public function test_guest_di_arahkan_ke_login_dari_root(): void
    {
        $this->get('/')->assertRedirect(route('login'));
    }
}