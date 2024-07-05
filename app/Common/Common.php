<?php

namespace App\Common;

use App\Models\Activation;
use Carbon\Carbon;

class Common
{
    /**
     * Store User
     * @param $request
     * @return mixed
     * @throws Exception
     */
    public function storeActivetion($id)
    {
        $data = [
            'user_id' => $id,
            'code' => sprintf('%06d', rand(1, 999999)),
            'expired_time' => Carbon::now()->addMinutes(config('constants.expiredCodeUser')),
        ];
        return Activation::create($data);
    }
    /**
     * trim space fullsize + space halfsize
     *
     * @param string $str
     * @return string | mixed
     */
    public static function trimSpaces($str)
    {
        if (is_string($str) && $str) {
            $chars = '\s　';
            $str = preg_replace("/^[$chars]+/u", '', $str);
            $str = preg_replace("/[$chars]+$/u", '', $str);
        }

        return $str;
    }

    /**
     * Escape string in query like
     *
     * @param String $string
     * @return string
     */
    public static function escapeLike($string): string
    {
        $arySearch = array('\\', '%', '_');
        $aryReplace = array('\\\\', '\%', '\_');
        return str_replace($arySearch, $aryReplace, $string);
    }
}
