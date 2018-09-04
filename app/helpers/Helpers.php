<?php
namespace Property\Helpers;
/**
* helpers Class
*/
class Helpers
{

	public static function dateChange($date, $type)
	{
		return date($type, strtotime($date));
	}

	public static function dateIndo($date)
	{
		$hari = [
			'Sun' => 'Sunday',
			'Mon' => 'Monday',
			'Tue' => 'Tuesday',
			'Wed' => 'Wednesday',
			'Thu' => 'Thursday',
			'Fri' => 'Friday',
			'Sat' => 'Saturday',
		];

		$day = $hari[date('D', strtotime($date))];
		$tgl = date('d/m/Y', strtotime($date));
		return $day . '<br>' . $tgl;
	}

	public static function number($number)
	{
		if (!empty($number)) {
			return number_format($number, 2, ',', '.');
		} else {
			return 0;
		}
	}

	public static function numberPrint($number)
	{

		if (!empty($number)) {
			if ($number < 0) {
				$total = $number - $number - $number;
				return '('.number_format($total, 2, ',', '.').')';
			} else {
				return number_format($number, 2, ',', '.');
			}
		} else {
			return '0,00';
		}
	}

	public static function errorSend($type)
	{
		$result = '';
		switch ($type) {
			case 'token':
	            $result .= '<div class="alert alert-danger alert-dismissible">';
                $result .= '<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>';
                $result .= '<h4><i class="icon fa fa-ban"></i>Error 550 - Permission denied</h4>';
                $result .= 'Invalid Token</div>';
				break;
			case 'user':
	            $result .= '<div class="alert alert-danger alert-dismissible">';
                $result .= '<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>';
                $result .= 'Invalid Username and Password</div>';
				break;
			case 'email':
	            $result .= '<div class="alert alert-danger alert-dismissible">';
                $result .= '<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>';
                $result .= 'Invalid email address</div>';
                break;
			case 'reset':
	            $result .= '<div class="alert alert-success alert-dismissible">';
                $result .= '<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>';
                $result .= 'Email sent. Check your email to get instructions for reseting password</div>';
                break;
            case 'error':
	            $result .= '<div class="alert alert-danger alert-dismissible">';
                $result .= '<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>';
                $result .= 'Unable to save new password</div>';
                break;
            case 'success':
	            $result .= '<div class="alert alert-success alert-dismissible">';
                $result .= '<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>';
                $result .= 'Your password was successfully changed</div>';
                break;
			case 'account':
			//default:
	            $result .= '<div class="alert alert-danger alert-dismissible">';
                $result .= '<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>';
                $result .= 'There is no account associated to this email</div>';
				break;
		}

		return $result;
	}

	public static function notify($type, $text)
	{
		switch ($type) {
			case 'error':
	            $notify = [
	                'title' => 'Errors',
	                'text'  => $text,
	                'type'  => 'error'
	            ];
				break;
			case 'warning':
				$notify = [
		            'title' => 'Warning',
		            'text'  => $text,
		            'type'  => 'warning',
		        ];
				break;
			case 'success':
				$notify = [
		            'title' => 'Success',
		            'text'  => $text,
		            'type'  => 'success',
		        ];
				break;
		}
		return $notify;
	}

	public static function usergroup($string)
	{
		$result = explode(',', $string);
        return $result;
	}

	public static function provinsi()
	{
   		$prov = DataWilayah::findByIdLevelWil("1");
		$tag  = '<option value="">Pilih Provinsi</option>';
   		foreach ($prov as $key => $value) {
				$tag .= '<option value="' . $value->id . '">' . $value->name . '</option>';
   		}
      
      return $tag;
	}

}