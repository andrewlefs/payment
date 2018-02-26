<?php


namespace Misc\Http\Adapter\Curl;

class Curl extends AbstractCurl {

  /**
   * @throws \RuntimeException
   */
  public function __construct() {
    parent::__construct();
    if (version_compare(PHP_VERSION, '5.5.0') >= 0) {
      throw new \RuntimeException("Unsupported Curl version");
    }
  }

  /**
   * @param string $string
   * @return string
   */
  public function escape($string) {
    return rawurlencode($string);
  }

  /**
   * @param int $bitmask
   * @return int
   */
  public function pause($bitmask) {
    return 0;
  }

  /**
   * @param string $filepath
   * @return string
   */
  public function preparePostFileField($filepath) {
    return "@".$filepath;
  }

  /**
   * @return void
   */
  public function reset() {
    $this->handle && curl_close($this->handle);
    $this->handle = curl_init();
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
