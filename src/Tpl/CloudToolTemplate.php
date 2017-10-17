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
        if ( ! file_put_contents($target, $out))
            throw new \Exception("Cannot write file: '$target'");
        return true;
    }


    public function parse(string $filename) {
        if ( ! file_exists($filename))
            throw new \Exception("File not found: '$filename'");
        $this->textTemplate->loadTemplate($filename);
        $ret = $this->textTemplate->apply([]);

        if (isset ($this->fnExtension->template["target"]))
            return $this->_templateToTarget($ret, $this->fnExtension->template);
        echo $ret;
    }


}