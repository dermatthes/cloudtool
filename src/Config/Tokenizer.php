<?php
    /**
     * Created by PhpStorm.
     * User: matthes
     * Date: 20.10.16
     * Time: 13:35
     */

     namespace CloudTool\Config;


     class Tokenizer {

         private $stmtStart;
         private $stmtClose;

         private $mCallback = null;

         public function __construct ($stmtStart = "{{", $stmtClose="}}") {
             $this->stmtStart = preg_quote($stmtStart);
             $this->stmtClose = preg_quote($stmtClose);
         }


         public function setCallack (callable $callback) {
             $this->mCallback = $callback;
         }



         private function parseSingleLine ($lineNo, $input) {
             return preg_replace_callback("/{$this->stmtStart}(.*?){$this->stmtClose}/i", function ($matches) use ($lineNo) {
                return ($this->mCallback)(trim ($matches[1]), $lineNo);
             }, $input);
         }

         public function parse ($input) {
             $input = explode("\n", $input);
             $ret = [];
             for ($i = 0; $i< count ($input); $i++) {
                 $ret[] = $this->parseSingleLine($i+1, $input[$i]);
             }
             return implode("\n", $ret);
         }


     }