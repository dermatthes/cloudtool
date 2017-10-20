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

        $this->log("fetch url='$url'...");
        if ( ! isset ($this->fetchCache[$url])) {
            $this->log("=> loading from url (not cached)");
            $this->fetchCache[$url] = file_get_contents($url);
        }
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
        if ( ! getenv($name))
            throw new \Exception("Unset environment variable requested by env name='$name'");
        return getenv($name);
    }


    public $template = [];

    public function template($args) {
        foreach ($args as $key => $value)
            $this->template[$key] = $value;
        return null;
    }


    public function log ($msg) {
        echo "[" . date ("Y-m-d H:i:s") . "] LOG: " . $msg . "\n";
    }

    public function warn ($msg) {
        echo "[" . date ("Y-m-d H:i:s") . "] WARN: " . $msg . "\n";
    }

    public function err ($msg) {
        echo "[" . date ("Y-m-d H:i:s") . "] !!!ERR: " . $msg . "\n";
    }


    public function resolve ($args) {
        $ips = gethostbynamel($args["hostname"]);
        if ($ips === false)
            return [];
        asort($ips);
        return $ips;
    }


    public function explode ($args) {
        return explode($args["delimiter"], $args["input"]);
    }

}