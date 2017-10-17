<?php
/**
 * Created by PhpStorm.
 * User: matthes
 * Date: 16.10.17
 * Time: 15:14
 */

namespace CloudTool\Tpl;


use Leuffen\TextTemplate\TextTemplate;

class CloudToolTemplate
{

    /**
     * @var TextTemplate
     */
    private $textTemplate;

    /**
     * @var TextTemplateExtension
     */
    private $fnExtension;

    public function __construct()
    {
        $this->textTemplate = new TextTemplate();
        $this->textTemplate->addFunctionClass($this->fnExtension = new TextTemplateExtension());
    }


    public function _templateToTarget(string $out, $opts) {
        $target = $this->fnExtension->template["target"];
        $oldFile = file_get_contents($target);

        if ( ! file_put_contents($target, $out))
            throw new \Exception("Cannot write file: '$target'");

        if ($oldFile !== $out) {
            $this->fnExtension->log("File '$target': Content changed.");
            if (isset ($this->fnExtension->template["onupdate"])) {
                $onUpdate = $this->fnExtension->template["onupdate"];
                $this->fnExtension->log("Running onupdate script '$onUpdate'...");
                exec ($onUpdate, $output, $retVal);
                if ($retVal === 0) {
                    $this->fnExtension->log("Success! (Output: " . implode("\n", $output));
                } else {
                    $this->fnExtension->warn("onupdate-script returned: $retVal (Output: " . implode("\n", $output));
                }
            }
        } else {
            $this->fnExtension->log("File '$target': Content not changed.");
        }
        return true;
    }


    public function parse(string $filename, bool $debug=false) {
        if ( ! file_exists($filename))
            throw new \Exception("File not found: '$filename'");
        $this->textTemplate->loadTemplate(file_get_contents($filename));
        $ret = $this->textTemplate->apply([]);
        echo $ret;

        if (isset ($this->fnExtension->template["target"]) && $debug === false)
            return $this->_templateToTarget($ret, $this->fnExtension->template);
        return $ret;
    }


    public function path ($dirname, $debug=false) {
        $files = glob($dirname . "/*.ctt");
        $ret = "";
        foreach ($files as $file) {
            $this->fnExtension->log("==> Processing file: $file");
            try {
                $ret .= $this->parse($file, $debug);
            } catch (\Exception $e) {
                $this->fnExtension->err($e->getMessage());
                throw $e;
            }
            $this->fnExtension->log("<== Done file: $file");
        }
        $this->fnExtension->log("<== DONE!");
        return $ret;
    }


}