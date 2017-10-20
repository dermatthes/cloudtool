<?php
/**
 * Created by PhpStorm.
 * User: matthes
 * Date: 20.10.17
 * Time: 12:23
 */

namespace CloudTool\Helper;


class HttpHelper
{

    public static function POST($url, $content, $opts=[]) {
        $options = array(
            'http' => array(
                'header'  => "Content-type: text/json\r\n",
                'method'  => 'POST',
                'content' => $content
            )
        );
        $context  = stream_context_create($options);
        $result = file_get_contents($url, false, $context);
        if ($result === FALSE) {
            throw new \Exception("Post request to $url failed.");
        }
        return $result;
    }

}