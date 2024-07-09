<?php
namespace System\Http\Handler;
use System\Http\Data\Request;
use System\Structure\HashCollectionImmutable;

class RequestHandler
{
    public function makeFromPost(): Request
    {
        $params = $_POST;
        $prepared_params = $this->prepareRequestParams($params);

        return Request::getInstance($prepared_params);
    }

    public function makeFromGet(): Request
    {
        $params = $_GET;
        $prepared_params = $this->prepareRequestParams($params);

       return Request::getInstance($prepared_params);
    }

    public function makeFromJsonPayload()
    {
        $params = file_get_contents('php://input');
        $params = json_decode($params, true);

        $prepared_params = $this->prepareRequestParams($params);

        return Request::getInstance($prepared_params);
    }

    protected function prepareRequestParams(array $params): array
    {
        $prepared_params = [];
        $prepared_param = null;

        foreach ($params as $param_key => $param_value) {
            $prepared_param = (is_array($param_value)) ? $this->prepareRequestParams($param_value) : trim(htmlspecialchars($param_value));

            if (is_array($param_value)) {
                $prepared_param = HashCollectionImmutable::getInstance($this->prepareRequestParams($param_value));
            }
            else {
                $prepared_param = trim(htmlspecialchars($param_value));
            }

            $prepared_params[$param_key] = $prepared_param;    
        }

        return $prepared_params;
    }


    protected function createRequest(array $params): Request
    {
        $request = Request::getInstance($params);

        foreach ($params as $param_key => $param_value) {
            $request->$param_key = $param_value;
        }

        return $request;
    }

}