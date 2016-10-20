<?php
    /**
     * Created by PhpStorm.
     * User: matthes
     * Date: 20.10.16
     * Time: 15:27
     */


    namespace CloudTool\WebServer;

    use IPSet\IPSet;

    class Router {

        private $allowedCidr = [];
        private $routes = [];


        /**
         * @param $path
         * @param callable $fn
         * @return $this
         */
        public function route ($path, callable $fn) {
            $this->routes[] = [$path, $fn];
            return $this;
        }


        /**
         * @param $cidr
         * @return $this
         */
        public function allowNet($cidr) {
            $this->allowedCidr[] = $cidr;
            return $this;
        }



        public static function Log ($message) {
            file_put_contents("php://stdout", "\n[" . date("Y-m-d H:i:s") . "]: " . $message);
        }

        public static function Fail ($statusCode, $message) {
            header ("HTTP/1.0 $statusCode Not Found");
            echo $message;
            exit;
        }


        public function serve () {

            if ( ! (new IPSet($this->allowedCidr))->match($_SERVER["REMOTE_ADDR"])) {
                self::Log("[ERROR]: Request from {$_SERVER["REMOTE_ADDR"]} not allowed.");
                self::Fail(500, "Access denied");
            }

            $requestUri = $_SERVER["REQUEST_URI"];
            foreach ($this->routes as $cur) {
                if (fnmatch($cur[0], $requestUri)) {
                    self::Log("[Client: {$_SERVER["REMOTE_ADDR"]}][Route-Match]: {$cur[0]}");
                    try {
                        $data = ($cur[1])($requestUri);
                        self::Log("[Client: {$_SERVER["REMOTE_ADDR"]}][OK]: $data");
                        echo $data;
                        exit;
                    } catch (\Exception $e) {
                        self::Log("[Client: {$_SERVER["REMOTE_ADDR"]}][Exception]: {$e->getMessage()}");
                        self::Fail(500, $e->getMessage());

                    }

                }
            }
            self::Log("[Client: {$_SERVER["REMOTE_ADDR"]}][Exception]: No route: $requestUri");
            self::Fail(404, "No Route defined");
        }



    }