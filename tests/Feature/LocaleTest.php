<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LocaleTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_switch_locale_to_urdu(): void
    {
        $response = $this->get(route('locale.switch', 'ur'));

        $response->assertRedirect();
        $response->assertSessionHas('locale', 'ur');
        $response->assertCookie('locale', 'ur');
    }

    public function test_user_can_switch_locale_to_spanish(): void
    {
        $response = $this->get(route('locale.switch', 'es'));

        $response->assertRedirect();
        $response->assertSessionHas('locale', 'es');
        $response->assertCookie('locale', 'es');
    }

    public function test_user_can_switch_locale_to_english(): void
    {
        $response = $this->get(route('locale.switch', 'en'));

        $response->assertRedirect();
        $response->assertSessionHas('locale', 'en');
        $response->assertCookie('locale', 'en');
    }

    public function test_unsupported_locale_falls_back_to_default_locale(): void
    {
        $response = $this->get(route('locale.switch', 'invalid_lang'));

        $response->assertRedirect();
        $response->assertSessionHas('locale', 'en');
    }

    public function test_authenticated_user_saves_preferred_language(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('locale.switch', 'ur'));

        $response->assertRedirect();
        $this->assertEquals('ur', session('locale'));
        $this->assertEquals('ur', $user->fresh()->getPreference('locale'));
    }

    public function test_page_renders_with_rtl_and_urdu_translations_when_urdu_locale_is_active(): void
    {
        $response = $this->withSession(['locale' => 'ur'])->get(route('home'));

        $response->assertStatus(200);
        $response->assertSee('dir="rtl"', false);
        $response->assertSee('lang="ur"', false);
        $response->assertSee('ہوم'); // Home in Urdu
        $response->assertSee('پک اپ کارٹ'); // Pickup Cart in Urdu
    }

    public function test_page_renders_with_spanish_translations_when_spanish_locale_is_active(): void
    {
        $response = $this->withSession(['locale' => 'es'])->get(route('home'));

        $response->assertStatus(200);
        $response->assertSee('dir="ltr"', false);
        $response->assertSee('lang="es"', false);
        $response->assertSee('Inicio'); // Home in Spanish
        $response->assertSee('Carrito de Recogida'); // Pickup Cart in Spanish
    }
}
