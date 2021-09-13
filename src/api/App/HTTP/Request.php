<?php

namespace App\HTTP;

use App\HTTP;
use App\JsonApi\Document\Errors;
use App\JsonApi\JsonApiException;
use App\Utils\JSON;
use App\Utils\JSON\JSONObject;

class Request
{

  public function getHeaders()
  {
    return apache_request_headers();
  }

  public function getHeader($name)
  {
    $headers = $this->getHeaders();

    if (isset($headers[$name]))
      return $headers[$name];

    return null;
  }

  public function getMethod()
  {
    return $_SERVER['REQUEST_METHOD'];
  }

  public function getURI()
  {
    return $_GET['url'];
  }

  public function getInput()
  {
    return file_get_contents('php://input');
  }

  public function getJSONObject(): JSONObject
  {
    $json = $this->getInput();
    if (JSON::isValid($json)) {
      $jsonObject = new JSONObject($json);

      if ($jsonObject->has('REQUEST_METHOD')) {
        return $jsonObject->optJSONObject('data');
      }

      return $jsonObject;
    }

    $json = $_POST['data'];
    if (JSON::isValid($json))
      return new JSONObject($json);

    throw new Errors(
      new JsonApiException(
        null,
        null,
        HTTP::CODE_BAD_REQUEST,
        null,
        "Invalid Json body",
        json_last_error_msg(),
        null,
        null,
        null
      )
    );
  }
}
