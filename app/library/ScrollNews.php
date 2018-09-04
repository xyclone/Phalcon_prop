<?php
namespace Property\Library;

use Phalcon\Di;
use Phalcon\Acl;
use Phalcon\Mvc\User\Component;

use Property\Models\News;

class ScrollNews extends Component
{
	public function getAllNews()
	{
		$response = "";
		$allNews = News::find([
			"columns" => "name,link,news,start_date",
			"conditions" => "active = 'Y' AND NOW() BETWEEN start_date AND stop_date"
		]);
		if($allNews&&$allNews->count()>0) {
			foreach ($allNews as $key => $value) {
				$response .="<li><span>".date("F j, Y", strtotime($value->start_date))."</span><a href='".$value->link."'>".$value->news."</a></li>";
			}
			return $response;
		}
		return $response;
	}
}