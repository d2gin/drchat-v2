<?php

namespace App\Api\Http;

use App\Api\Services\StatusCode;
use Illuminate\Http\JsonResponse;

/**
 * @method static ApiResponse code($code)
 * @method static ApiResponse data($data)
 * @method static ApiResponse message($msg)
 * Class ApiResponse
 * @package App\Api\Http
 */
class ApiResponse extends JsonResponse
{
    protected $dataset = null;
    protected $message = "";
    protected $code    = 0;

    public function __construct($data = null, $status = 200, $headers = [], $options = 0, $json = false)
    {
        parent::__construct($data, $status, $headers, $options, $json);
    }

    protected function format()
    {
        $this->setData([
            'code'    => $this->code,
            'message' => $this->message,
            "data"    => $this->dataset,
        ]);
        return $this;
    }

    public static function error($msg, $data = null)
    {
        return self::code(StatusCode::STATUS_NORMAL_ERROR)->data($data)->message($msg);
    }

    public static function success($data = null, $msg = 'success')
    {
        if (!is_object($data) && !is_array($data)) {
            $msg  = $data;
            $data = null;
        }
        return self::code(StatusCode::STATUS_SUCCESS)->data($data)->message($msg);
    }

    public function __call($method, $parameters)
    {
        if ($method == 'data') {
            // 为了不影响父类的data属性
            $method = 'dataset';
        }
        $this->$method = array_shift($parameters);
        $this->format();
        return $this;
    }

    public static function __callStatic($name, $args)
    {
        if ($name == 'data') {
            // 为了不影响父类的data属性
            $name = 'dataset';
        }
        $instance        = new static();
        $instance->$name = array_shift($args);
        $instance->format();
        return $instance;
    }
}
