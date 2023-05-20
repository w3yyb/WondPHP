<?php
namespace WondPHP\Http\Protocols;
// 此文件目前无用

/**
 * Class Chunk
 * @package 
 */
class Chunk
{
    /**
     * Chunk buffer.
     *
     * @var string
     */
    protected $_buffer = null;

    /**
     * Chunk constructor.
     * @param $buffer
     */
    public function __construct($buffer)
    {
        $this->_buffer = $buffer;
    }

    /**
     * __toString
     *
     * @return string
     */
    public function __toString()
    {
        return \dechex(\strlen($this->_buffer))."\r\n$this->_buffer\r\n";
    }
}