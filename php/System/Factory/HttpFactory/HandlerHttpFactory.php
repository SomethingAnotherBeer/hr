<?php
namespace System\Factory\HttpFactory;
use System\Http\Handler\RequestHandler;
use System\Http\Data\Request;


class HandlerHttpFactory
{
    public static function makeRequestHandler(): RequestHandler
    {
        return new RequestHandler();
    }
}   