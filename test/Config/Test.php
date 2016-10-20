<?php
    /**
     * Created by PhpStorm.
     * User: matthes
     * Date: 20.10.16
     * Time: 13:30
     */

    namespace CloudTool\Config;


    class Test extends \PHPUnit_Framework_TestCase
    {


        public function testBasic () {

            $tool = new TextTool();
            echo $tool->parse(__DIR__ . "/mock/main.in.yml");



        }


    }
