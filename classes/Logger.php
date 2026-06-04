<?php
class Logger {
    private static $log_file = __DIR__ . '/../logs/system.log';

    // ლოგის ჩაწერა ფაილში
    public static function log($message, $user_email = 'სისტემა') {
        $date = date('Y-m-d H:i:s');
        $formatted_message = "[{$date}] [მომხმარებელი: {$user_email}] - {$message}" . PHP_EOL;
        
        // ჩაწერა ფაილის ბოლოში (APPEND)
        file_put_contents(self::$log_file, $formatted_message, FILE_APPEND);
    }

    // ლოგების წაკითხვა ადმინ პანელისთვის
    public static function readLogs() {
        if (file_exists(self::$log_file)) {
            return file_get_contents(self::$log_file);
        }
        return "ლოგების ფაილი ცარიელია ან არ არსებობს.";
    }
}