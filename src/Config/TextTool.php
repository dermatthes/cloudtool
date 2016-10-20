<?php
    /**
     * Created by PhpStorm.
     * User: matthes
     * Date: 20.10.16
     * Time: 12:48
     */
    namespace CloudTool\Config;

    use Symfony\Component\ExpressionLanguage\ExpressionLanguage;

    class TextTool {

        private $exprLang;
        private $tokenizer;

        private $vars = [];

        public function __construct(Tokenizer $tokenizer = null)
        {
            if ($tokenizer === null) {
                $tokenizer = new Tokenizer();
                $tokenizer->setCallack(function ($stmt, $lineNo) {
                    try {
                        return $this->exprLang->evaluate($stmt, $this->vars);
                    } catch (\Exception $e) {
                        throw new \Exception("Exception in line: {$lineNo} (parsing: '{$stmt}': {$e->getMessage()}", $e->getCode(), $e);
                    }
                });
            }
            $this->tokenizer = $tokenizer;
            $this->exprLang = new ExpressionLanguage();
            foreach (get_class_methods(TextToolDefaultFunctions::class) as $curMethod) {
                $this->registerFunction($curMethod, [TextToolDefaultFunctions::class, $curMethod]);
            }


        }


        /**
         * Register a function
         *
         * @param $name
         * @param callable $callback
         * @return $this
         */
        public function registerFunction ($name, callable $callback) {
            $this->exprLang->register($name, function () {}, function ($vars, ...$params) use ($callback) { return $callback(...$params); });
            return $this;
        }


        /**
         * Set variable
         *
         * @param $name
         * @param $value
         * @return $this
         */
        public function set($name, $value) {
            $this->vars[$name] = $value;
            return $this;
        }


        /**
         * @param $filename
         * @return string
         */
        public function parse($filename) {
            if ( ! substr($filename, 0, 1) === "/") {
                $filename = getcwd() . "/" . $filename;
            }

            $curTool = TextToolDefaultFunctions::$curTextTool;
            TextToolDefaultFunctions::$curTextTool = $this;
            $curCwd = TextToolDefaultFunctions::$curCwd;
            TextToolDefaultFunctions::$curCwd = dirname($filename);

            $ret =  $this->tokenizer->parse(file_get_contents($filename));

            TextToolDefaultFunctions::$curCwd = $curCwd;
            TextToolDefaultFunctions::$curTextTool = $curTool;
            return $ret;
        }



    }