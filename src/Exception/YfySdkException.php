<?php
/**
 * Created by PhpStorm.
 * User: just-cj
 * Date: 2017/6/5
 * Time: 14:26
 */

namespace Fangcloud\Exception;


/**
 * Class YfySdkException
 * @package Fangcloud\Exception
 */
class YfySdkException extends \Exception
{
    /* @var array */
    private $errors;
    /* @var string */
    private $requestId;

    /**
     * YfySdkException constructor.
     * @param string|null $message
     * @param string $errors
     * @param string $requestId
     */
    public function __construct($message = null, $errors = null, $requestId = null)
    {
        $this->errors = $errors;
        $this->requestId = $requestId;
        if (!$message) {
            if (!$errors) {
                parent::__construct('unknown error');
            }
            parent::__construct($this->__toString());
        }
        else {
            parent::__construct($message);
        }
    }


    /**
     * @return string
     */
    public function getRequestId()
    {
        return $this->requestId;
    }

    /**
     * @param string $requestId
     */
    public function setRequestId($requestId)
    {
        $this->requestId = $requestId;
    }

    /**
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * @param array $errors
     */
    public function setErrors($errors)
    {
        $this->errors = $errors;
    }

    public function __toString()
    {
        return 'errors: ' . json_encode($this->errors) . ', request_id: ' . $this->requestId;
    }

}