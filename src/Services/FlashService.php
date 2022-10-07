<?php

namespace App\Services;

const FLASH = 'FLASH_MESSAGES';

const FLASH_ERROR = 'danger';
const FLASH_WARNING = 'warning';
const FLASH_INFO = 'primary';
const FLASH_SUCCESS = 'success';

class FlashService
{
    /**
     * Create a flash message
     *
     * @param string $name
     * @param string $message
     * @param string $type
     * @return void
     */
    private static function create_flash_message(string $name, string $message, string $type): void
    {
        // remove existing message with the name
        if (isset($_SESSION[FLASH][$name])) {
            unset($_SESSION[FLASH][$name]);
        }
        // add the message to the session
        $_SESSION[FLASH][$name] = ['message' => $message, 'type' => $type];
    }
    
    
    /**
     * Format a flash message
     *
     * @param array $flash_message
     * @return string
     */
    private static function format_flash_message(array $flash_message): string
    {
        return sprintf('<div class="alert alert-%s" role="alert">%s</div>',
            $flash_message['type'],
            $flash_message['message']
        );
    }
    
    /**
     * Display a flash message
     *
     * @param string $name
     * @return void
     */
    private static function display_flash_message(string $name): void
    {
        if (!isset($_SESSION[FLASH][$name])) {
            return;
        }
        
        // get message from the session
        $flash_message = $_SESSION[FLASH][$name];
        
        // delete the flash message
        unset($_SESSION[FLASH][$name]);
        
        // display the flash message
        echo  self::format_flash_message($flash_message);
    }
    
    /**
     * Display all flash messages
     *
     * @return void
     */
    private static function display_all_flash_messages(): void
    {
        if (!isset($_SESSION[FLASH])) {
            return;
        }
        
        // get flash messages
        $flash_messages = $_SESSION[FLASH];
        
        // remove all the flash messages
        unset($_SESSION[FLASH]);
        
        // show all flash messages
        foreach ($flash_messages as $flash_message) {
            echo  self::format_flash_message($flash_message);
        }
    }
    
    /**
     * Flash a message
     *
     * @param string $name
     * @param string $message
     * @param string $type (error, warning, info, success)
     * @return void
     */
    public static function flash(string $name = '', string $message = '', string $type = ''): void
    {
        if ($name !== '' && $message !== '' && $type !== '') {
            // create a flash message
            self::create_flash_message($name, $message, $type);
        } elseif ($name !== '' && $message === '' && $type === '') {
            // display a flash message
            self::display_flash_message($name);
        } elseif ($name === '' && $message === '' && $type === '') {
            // display all flash message
            self::display_all_flash_messages();
        }
    }
}
