<?php


namespace Misc\Object;

use Misc\Http\RequestInterface;
use Misc\Object\Traits\CannotUpdate;

abstract class AbstractAsyncJobObject extends AbstractCrudObject {
  use CannotUpdate;

  /**
   * @return string
   */
  abstract protected function getCreateIdFieldName();

  /**
   * @return string
   */
  protected function getCompletitionPercentageFieldName() {
    return 'async_percent_completion';
  }

  /**
   * Create function for the object.
   *
   * @param array $params Additional parameters to include in the request
   * @return $this
   * @throws \Exception
   */
  public function create(array $params = array()) {
   
  }

  /**
   * This method won't fetch new data, you are required to call read() before
   * @return bool
   */
  public function isComplete() {
    return $this->{$this->getCompletitionPercentageFieldName()} === 100;
  }

  /**
   * @param array $fields
   * @param array $params
   *
   * @return mixed
   */
  abstract public function getResult(
    array $fields = array(), array $params = array());
}
