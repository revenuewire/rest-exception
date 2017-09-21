<?php

namespace Rest;

use Throwable;

class Exception extends \Exception
{
    public $context = array();
    public $key = null;

    const KEY_LENGTH = 50;

    /**
     * RestException constructor.
     *
     * @param string $message
     * @param string $key
     * @param array $context
     * @param int $code
     * @param Throwable|null $previous
     */
    function __construct($message, $key = null, $context = array(), $code = 0, Throwable $previous = null)
    {
        $this->context = $context;
        if (empty($key)) {
            $messageKey = self::slugify($message);
            if (strlen($messageKey) > self::KEY_LENGTH) {
                $this->key = substr($messageKey, 0, self::KEY_LENGTH) . "-" . hash_hmac("crc32", substr($messageKey, self::KEY_LENGTH+1), "");
            } else {
                $this->key = $messageKey;
            }
        } else {
            $this->key = $key;
        }

        parent::__construct($message, $code, $previous);
    }

    /**
     * @return null|string
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * @param null|string $key
     */
    public function setKey($key)
    {
        $this->key = $key;
    }


    /**
     * @return array
     */
    public function getContext()
    {
        return $this->context;
    }

    /**
     * @param array $context
     */
    public function setContext($context)
    {
        $this->context = $context;
    }

    /**
     * slugify a given string, It was taken from symfony's jobeet tutorial.
     *
     * @param $text
     * @return mixed|string
     */
    public static function slugify($text)
    {
        // replace non letter or digits by -
        $text = preg_replace('~[^\\pL\d]+~u', '-', $text);

        // trim
        $text = trim($text, '-');

        // transliterate
        $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);

        // lowercase
        $text = strtolower($text);

        // remove unwanted characters
        $text = preg_replace('~[^-\w]+~', '', $text);

        if (empty($text)) {
            return '';
        }

        return $text;
    }
}