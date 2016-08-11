<?php

/**
 * @since      1.0.0
 * @package    Donation_Button
 * @subpackage Donation_Button/includes
 * @author     mbj-webdevelopment <mbjwebdevelopment@gmail.com>
 */
class Donation_Button_Activator {

    /**
     * @since    1.0.0
     */
    public static function activate() {
        self::create_files();
    }

    private static function create_files() {
        $upload_dir = wp_upload_dir();
        $files = array(
            array(
                'base' => DBP_FOR_WORDPRESS_LOG_DIR,
                'file' => '.htaccess',
                'content' => 'deny from all'
            ),
            array(
                'base' => DBP_FOR_WORDPRESS_LOG_DIR,
                'file' => 'index.html',
                'content' => ''
            )
        );
        foreach ($files as $file) {
            if (wp_mkdir_p($file['base']) && !file_exists(trailingslashit($file['base']) . $file['file'])) {
                if ($file_handle = @fopen(trailingslashit($file['base']) . $file['file'], 'w')) {
                    fwrite($file_handle, $file['content']);
                    fclose($file_handle);
                }
            }
        }
    }
}