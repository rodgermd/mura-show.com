<?php

namespace BCC\ExtraToolsBundle\Translation\Formatter;

class PhpFormatter implements FormatterInterface
{
    public function format(array $messages)
    {
        $output = "<?php\nreturn ".var_export($messages, true).";";

        return $output;
    }
}