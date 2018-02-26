<?php


namespace Misc\Http\Adapter\Curl;

class Curl55 extends AbstractCurl {

  /**
   * @throws \RuntimeException
   */
  public function __construct() {
    parent::__construct();
    if (version_compare(PHP_VERSION, '5.5.0') < 0) {
      throw new \RuntimeException("Unsupported Curl version");
    }
  }

  /**
   * @param string $string
   * @return bool|string
   */
  public function escape($string) {
    return curl_escape($this->handle, $string);
  }

  /**
   * @param int $bitmask
   * @return int
   */
  public function pause($bitmask) {
    return curl_pause($this->handle, $bitmask);
  }

  /**
   * @param string $filepath
   * @return \CURLFile
   */
  public function preparePostFileField($filepath) {
    return new \CURLFile($filepath);
  }

  /**
   * @return void
   */
  public function reset() {
    $this->handle && curl_reset($this->handle);
  }

  /**
   * @param int $errornum
   * @return NULL|string
   */
  public static function strerror($errornum) {
    return curl_strerror($errornum);
  }

  /**
   * @param string $string
   * @return bool|string
   */
  public function unescape($string) {
    return curl_unescape($this->handle, $string);
  }
}
