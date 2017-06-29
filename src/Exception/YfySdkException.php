<?php
/**
 * 通用sdk异常
 */

namespace Fangcloud\Exception;


/**
 * Class YfySdkException
 * @package Fangcloud\Exception
 */
class YfySdkException extends \Exception
{
    /**
     * @var string 错误码
     */
    protected $code;
    /**
     * @var array 请求返回的错误信息
     */
    private $errors;
    /**
     * @var string 请求返回的request id
     */
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
            $this->code = 'unknown_error';
            if (isset($errors[0]) && isset($errors[0]['code'])) $this->code = $errors[0]['code'];
            parent::__construct($this->__toString());
        }
        else {
            parent::__construct($message);
        }
    }


    /**
     * 获取request id
     *
     * @return string
     */
    public function getRequestId()
    {
        return $this->requestId;
    }

    /**
     * 设置request id
     *
     * @param string $requestId
     */
    public function setRequestId($requestId)
    {
        $this->requestId = $requestId;
    }

    /**
     * 获取错误信息
     *
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * 设置错误信息
     *
     * @param array $errors
     */
    public function setErrors($errors)
    {
        $this->errors = $errors;
    }



    /**
     * 重载__toString方法
     *
     * @return string
     */
    public function __toString()
    {
        return 'errors: ' . json_encode($this->errors) . ', request_id: ' . $this->requestId;
    }

}