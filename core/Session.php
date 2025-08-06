<?php

namespace Core;

class Session
{
    public static function start()
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_set_cookie_params([
                'lifetime' => 1800, // 30 minutes,
                'path' => '/',
                'domain' => '', // Set your domain in production
                'secure' => isset($_SERVER['HTTPS']), // Only send over HTTPS
                'httponly' => true // Prevent Javascript access
            ]);

            session_start();
        }
    }

    public static function set($key, $value)
    {
        $_SESSION[$key] = $value;
    }

    public static function get($key, $default = null)
    {
        return $_SESSION[$key] ?? $default;
    }

    public static function has($key)
    {
        return isset($_SESSION[$key]);
    }

    public static function remove($key)
    {
        unset($_SESSION[$key]);
    }

    public static function destroy()
    {
        session_unset();
        session_destroy();
    }

    public static function isAuthenticated()
    {
        return self::has('user_id');
    }

    /**
     * Set a flash message that will be available for the next request.
     * 
     * @param string $key The key for the flash message
     * @param string $message The message to display
     */

    public static function flash($key, $message)
    {
        self::set('flash_' . $key, $message);
    }

    /**
     * Check if a flash message exists, and return it.
     * Also, unsets it so it's only shown once
     * 
     * @param string $key The key for the message
     * @return string|null The message or null if not found
     */

    public static function getFlash($key)
    {
        $message = self::get('flash_' . $key);

        if ($message) {
            self::remove('flash_' . $key);
        }

        return $message;
    }
}
