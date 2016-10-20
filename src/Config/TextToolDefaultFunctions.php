<?php
    /**
     * Created by PhpStorm.
     * User: matthes
     * Date: 20.10.16
     * Time: 12:59
     */
    namespace CloudTool\Config;


    class TextToolDefaultFunctions {

        public static $curCwd;


        public static $curTextTool;

        /**
         * Load Data from environment
         *
         * @param $name
         */
        public static function ENV($name) {
            return $_ENV[$name];
        }

        public static function REMOTE_IP() {
            return $_SERVER["REMOTE_ADDR"];
        }

        public static function SERVER_IP() {

        }

        public static function BASE64_ENC($input) {
            return base64_encode($input);
        }


        public static function TRIM ($input) {
            return trim ($input);
        }


        public static function FILE($filename) {
            if (substr($filename, 0, 1) !== "/") {
                $filename = self::$curCwd . "/" . $filename;
            }
            if ( ! file_exists($filename))
                throw new \Exception("File not found: '$filename'");
            return file_get_contents($filename);
        }


        public static function INCLUDE ($filename) {
            if (substr($filename, 0, 1) !== "/") {
                $filename = self::$curCwd . "/" . $filename;
            }
            if ( ! file_exists($filename))
                throw new \Exception("File not found: '$filename'");
            return self::$curTextTool->parse($filename);
        }

    }