<?php
namespace Simcify\Middleware;

use Pecee\Http\Middleware\IMiddleware;
use Pecee\Http\Request;

/**
 * Class FilterRequestParameters
 * @package Simcify\Middleware
 */
class FilterRequestParameters implements IMiddleware {


    /**
     * Check the inputs and filter out the special characters to ignore data leaks
     * @param Request $request
     * @return Request|void|null
     */
    public function handle(Request $request) {
        $_GET   = str_replace('}}', '', str_replace('{{', '', filter_input_array(INPUT_GET, FILTER_SANITIZE_STRING)));
        $_POST  = str_replace('}}', '', str_replace('{{', '', filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING)));
    }
}
