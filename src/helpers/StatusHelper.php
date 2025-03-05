<?php

/**
 * User: alaspina
 * Date: 21/02/2025
 * Time: 15:10
 */

namespace Livewire\Select2\Helpers;

class StatusHelper
{
    static function getStatusCodeText($status)
    {
        switch ($status) {
            case Response::STATUS_LOGIN_TIMEOUT;
                $statusText = 'LOGIN TIME-OUT';
                break;
            case Response::STATUS_UNAUTHORIZE;
                $statusText = 'UNAUTHORIZE';
                break;
            case Response::STATUS_BAD_REQUEST;
                $statusText = 'BAD REQUEST';
                break;
            case Response::STATUS_NOT_FOUND;
                $statusText = 'NOT FOUND';
                break;
            case Response::STATUS_OK;
                $statusText = 'OK';
                break;
            case Response::STATUS_FORBIDDEN;
                $statusText = 'FORBIDDEN';
                break;
        }

        return $statusText;
    }
}
