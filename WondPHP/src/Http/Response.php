<?php
namespace WondPHP\Http;
use WondPHP\Http\Protocols\Response as ResponseProtocols;
/**
 * Class Response
 *
 */
class Response extends ResponseProtocols
{
    /**
     * @param $file
     * @return $this
     */
    public function file($file)
    {
        if ($this->notModifiedSince($file)) {
            return $this->withStatus(304);
        }
        $this->echofile=$file;
        return $this->withFile($file);
    }

    /**
     * @param $file
     * @param string $download_name
     * @return $this
     */
    public function download($file, $download_name = '')
    {
        $this->withFile($file);
        if ($download_name) {
            $this->down_name=$download_name;
        }
        return $this;
    }

    /**
     * @param $file
     * @return bool
     */
    protected function notModifiedSince($file)
    {
        $if_modified_since = request()->header('if-modified-since');
        if ($if_modified_since === null || !($mtime = \filemtime($file))) {
            return false;
        }
        return $if_modified_since === \date('D, d M Y H:i:s', $mtime) . ' ' . \date_default_timezone_get();
    }
}
