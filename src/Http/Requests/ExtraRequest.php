<?php


namespace Golly\Authority\Http\Requests;

use Golly\Authority\Contracts\ExtraQueryInterface;

/**
 * Class ExtraRequest
 * @package Golly\Authority\Http\Requests
 */
class ExtraRequest extends ApiRequest implements ExtraQueryInterface
{

    /**
     * @return array
     */
    public function rules()
    {
        return [];
    }

    /**
     * @return void
     */
    public function addExtraData()
    {
        $this->query->set('user_id', null);
    }

}
