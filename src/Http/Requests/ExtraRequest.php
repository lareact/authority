<?php


namespace Golly\Authority\Http\Requests;

use Golly\Authority\Contracts\QueryInputInterface;
use Illuminate\Http\Request;

/**
 * Class ExtraRequest
 * @package Golly\Authority\Http\Requests
 */
class ExtraRequest extends Request implements QueryInputInterface
{

    /**
     *
     */
    public function addExtraData()
    {
        $this->query->add(['user_id' => null]);
    }

}
