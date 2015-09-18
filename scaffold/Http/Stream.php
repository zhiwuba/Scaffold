<?php

namespace Scaffold\Http;


/**
 * Describes a data stream.
 *
 * Typically, an instance will wrap a PHP stream; this interface provides
 * a wrapper around the most common operations, including serialization of
 * the entire stream to a string.
 */
class Stream
{
    /**
    *  @var resource $resource
    */
    protected $resource = null;

    /**
    *  @var array open mode
    */
    protected static $modes = [
        'readable' => ['r', 'r+', 'w+', 'a+', 'x+', 'c+'],
        'writable' => ['r+', 'w', 'w+', 'a', 'a+', 'x', 'x+', 'c', 'c+'],
    ];

    public function __construct($resource)
    {
        $this->resource=$resource;
    }

    public function __destruct()
    {
        $this->close();
    }

    /**
     * create from file for UploadedFile.
    * @param string $filename
     *@return Stream
     *@throws \Exception
    */
    public static function createFromFile($filename)
    {
        if( file_exists($filename) ){
            $resource=fopen($filename, 'w+');
            $stream=new Stream($resource);
            return $stream;
        }else{
            throw new \Exception("$filename doesn't exist");
        }
    }

    /**
    *  create from memory
     * @return Stream
    */
    public static function createFromMemory()
    {
        $resource=fopen('php://memory', 'r+');
        $stream=new Stream($resource);
        return $stream;
    }

    /**
    *  copy from input for request.
     * @return Stream
    */
    public static function createFromInput()
    {
        $resource=fopen('php://temp',  'w+');
        stream_copy_to_stream(fopen('php://input', 'r'), $resource);
        $stream=new Stream($resource);
        return $stream;
    }


    /**
     * Reads all data from the stream into a string, from the beginning to end.
     *
     * This method MUST attempt to seek to the beginning of the stream before
     * reading data and read the stream until the end is reached.
     *
     * Warning: This could attempt to load a large amount of data into memory.
     *
     * This method MUST NOT raise an exception in order to conform with PHP's
     * string casting operations.
     *
     * @see http://php.net/manual/en/language.oop5.magic.php#object.tostring
     * @return string
     */
    public function __toString()
    {
        fseek($this->resource, 0, SEEK_SET);
        $string=fread($this->resource, filesize($this->filename));
        return $string;
    }

    /**
     * Closes the stream and any underlying resources.
     *
     * @return void
     */
    public function close()
    {
        if( $this->resource!==NULL )
        {
            fclose($this->resource);
        }
    }

    /**
     * Separates any underlying resources from the stream.
     *
     * After the stream has been detached, the stream is in an unusable state.
     *
     * @return resource|null Underlying PHP stream, if any
     */
    public function detach()
    {
        $oldResource=$this->resource;
        fclose($this->resource);
        $this->resource=NULL;
        return $oldResource;
    }

    /**
     * Get the size of the stream if known.
     *
     * @return int|null Returns the size in bytes if known, or null if unknown.
     */
    public function getSize()
    {
        $stat=fstat($this->resource);
        if( isset($stat['size']) )
        {
            return $stat['size'];
        }
        else
        {
            return null;
        }
    }

    /**
     * Returns the current position of the file read/write pointer
     *
     * @return int Position of the file pointer
     * @throws \RuntimeException on error.
     */
    public function tell()
    {
        $pos=ftell($this->resource);
        if( $pos===FALSE )
        {
            throw new \RuntimeException("tell error");
        }
        else
        {
            return $pos;
        }
    }

    /**
     * Returns true if the stream is at the end of the stream.
     *
     * @return bool
     */
    public function eof()
    {
        $eof=feof($this->resource);
        return $eof;
    }

    /**
     * Returns whether or not the stream is seekable.
     *
     * @return bool
     */
    public function isSeekable()
    {
        $seekable=$this->getMetadata('seekable');
        return $seekable;
    }

    /**
     * Seek to a position in the stream.
     *
     * @link http://www.php.net/manual/en/function.fseek.php
     * @param int $offset Stream offset
     * @param int $whence Specifies how the cursor position will be calculated
     *     based on the seek offset. Valid values are identical to the built-in
     *     PHP $whence values for `fseek()`.  SEEK_SET: Set position equal to
     *     offset bytes SEEK_CUR: Set position to current location plus offset
     *     SEEK_END: Set position to end-of-stream plus offset.
     * @throws \RuntimeException on failure.
     */
    public function seek($offset, $whence = SEEK_SET)
    {
        $ret=fseek($this->resource, $offset, $whence);
        if( $ret==-1 )
        {
            throw new \RuntimeException("seek error");
        }
    }

    /**
     * Seek to the beginning of the stream.
     *
     * If the stream is not seekable, this method will raise an exception;
     * otherwise, it will perform a seek(0).
     *
     * @see seek()
     * @link http://www.php.net/manual/en/function.fseek.php
     * @throws \RuntimeException on failure.
     */
    public function rewind()
    {
        if( $this->isSeekable() )
        {
            $this->seek(0);
        }
        else
        {
            throw new \RuntimeException("rewind fail.");
        }
    }

    /**
     * Returns whether or not the stream is writable.
     *
     * @return bool
     */
    public function isWritable()
    {
        $mode=$this->getMetadata('mode');
        if( in_array($mode , self::$modes['writable'] ) )
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    /**
     * Write data to the stream.
     *
     * @param string $string The string that is to be written.
     * @return int Returns the number of bytes written to the stream.
     * @throws \RuntimeException on failure.
     */
    public function write($string)
    {
        $ret=fwrite($this->resource, $string);
        if( $ret===FALSE )
        {
            throw new \RuntimeException();
        }
    }

    /**
     * Returns whether or not the stream is readable.
     *
     * @return bool
     */
    public function isReadable()
    {
        $mode=$this->getMetadata('mode');
        if( in_array($mode , self::$modes['readable'] ) )
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    /**
     * Read data from the stream.
     *
     * @param int $length Read up to $length bytes from the object and return
     *     them. Fewer than $length bytes may be returned if underlying stream
     *     call returns fewer bytes.
     * @return string Returns the data read from the stream, or an empty string
     *     if no bytes are available.
     * @throws \RuntimeException if an error occurs.
     */
    public function read($length)
    {
        $ret=fread($this->resource, $length);
        if( $ret===FALSE )
        {
            throw new \RuntimeException('read file error');
        }
        return $ret;
    }

    /**
     * Returns the remaining contents in a string
     *
     * @return string
     * @throws \RuntimeException if unable to read or an error occurs while
     *     reading.
     */
    public function getContents()
    {
        if( $this->isReadable() )
        {
            $this->read($this->getSize());
        }
        else
        {
            throw new \RuntimeException("getContents error.");
        }
    }

    /**
     * Get stream metadata as an associative array or retrieve a specific key.
     *
     * The keys returned are identical to the keys returned from PHP's
     * stream_get_meta_data() function.
     *
     * @link http://php.net/manual/en/function.stream-get-meta-data.php
     * @param string $key Specific metadata to retrieve.
     * @return array|mixed|null Returns an associative array if no key is
     *     provided. Returns a specific key value if a key is provided and the
     *     value is found, or null if the key is not found.
     */
    public function getMetadata($key = null)
    {
        $meta=stream_get_meta_data($this->resource);
        if( $key===null )
        {
            return $meta;
        }
        else if( isset($meta[$key]))
        {
            return $meta[$key];
        }
        else
        {
            return null;
        }
    }

}
