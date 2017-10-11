<?php
/**
 * Created by PhpStorm.
 * User: matthes
 * Date: 11.10.17
 * Time: 14:44
 */

namespace CloudTool\Tpl;


use Leuffen\TextTemplate\TemplateParsingException;

class TextTemplateExtension
{

    private $fetchCache = [];


    /**
     * Load external URL
     *
     * @param $args
     *
     * @return mixed
     * @throws TemplateParsingException
     * @throws \Exception
     */
    public function fetch ($args) {
        $url = $args["url"];
        if ( ! preg_match ("/^https?\:\/\/.+/i", $url)) {
            if ( ! isset ($args["rootUrl"]))
                throw new \Exception("Invalid fetch url '$url'. (no rootUrl specified)");
            $rootUrl = $args["rootUrl"];
            if ( ! preg_match ("/^https?\:\/\/.+/i", $rootUrl)) {
                throw new \Exception("Invalid fetch rootUrl '$rootUrl'");
            }
            $rootUrl = dirname($rootUrl);
            $url = $rootUrl . "/" . $url;
        }
        $enc = isset ($args["enc"]) ? $args["enc"] : false;
        if ( ! isset ($this->fetchCache[$url]))
            $this->fetchCache[$url] = file_get_contents($url);
        $data = $this->fetchCache[$url];

        if ($enc === false)
            return $data;

        switch (strtoupper($enc)) {
            case "JSON":
                $data = json_decode($data, true);
                break;

            default:
                throw new TemplateParsingException("Invalid fetch enc='$enc' (Available: JSON)");
        }
        if ( ! is_array($data))
            throw new \Exception("Cannot fetch '$url' (JSON): Invalid!");
        return $data;
    }


    public function env ($args) {
        $name = @$args["name"];
        if ( ! isset ($_ENV[$name]))
            throw new \Exception("Unset environment variable requested by env name='$name'");
        return $_ENV[$name];
    }

}