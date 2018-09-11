<?php
namespace Property\Classes;

class SearchClass
{
	public function tenureOptions()
	{
		$response = [
			'99-yrs' => ['99-yrs', '92-yrs', '89-yrs', '85-yrs', '110-yrs', '104-yrs', '103-yrs', '102-yrs', '100-yrs'],
			'Freehold' => ['Freehold', '9999-yrs', '999999-yrs'],
			'999-yrs' => ['999-yrs', '998-yrs', '993-yrs', '956-yrs', '953-yrs', '947-yrs', '946-yrs', '945-yrs', '937-yrs', '929-yrs'],
			'70-yrs' => ['70-yrs'],
			'60-yrs' => ['60-yrs']
		];
		return $response;
	}

	public function unitOptions()
	{
		$response = [
			"1BR-All Types" => "%1BR%", 
			"2BR-All Types" => "%2BR%",
			"3BR-All Types" => "%3BR%", 
			"4BR-All Types" => "%4BR%", 
			"5BR-All Types" => "%5BR%",
			"Penthouse" => "%Penthouse%",
			"Terrace House" => "%Terrace House%",
			"Semi-Detached House" => "%Semi-Detached House%",
			"Detached House" => "%Detached House%",
			"Townhouse" => "%Townhouse%",
			"Villa" => "%Villa%",
			"Studio" => "Studio",
			"Studio+Study" => "Studio+Study",
			"1BR" => "1BR",
			"1BR+Study" => "1BR+Study",
			"1BR-Loft" => "1BR-Loft",
			"1BR-Loft+Study" => "1BR-Loft+Study",
			"1BR+PES" => "1BR+PES",
			"2BR" => "2BR",
			"2BR-Compact" => "2BR-Compact",
			"2BR-Premium" => "2BR-Premium",
			"2BR-Deluxe" => "2BR-Deluxe",
			"2BR-Dual-Key" => "2BR-Dual-Key",
			"2BR-Dual-Key+Study" => "2BR-Dual-Key+Study",
			"2BR-Dual-Key-SOHO" => "2BR-Dual-Key-SOHO",
			"2BR+Study" => "2BR+Study",
			"2BR+Study+PES" => "2BR+Study+PES",
			"2BR-Loft" => "2BR-Loft",
			"2BR-Loft+Study" => "2BR-Loft+Study",
			"2BR-Duplex+Study" => "2BR-Duplex+Study",
			"2BR+Guest" => "2BR+Guest",
			"2BR+PES" => "2BR+PES",
			"2BR+RoofTerrace" => "2BR+RoofTerrace",
			"3BR" => "3BR",
			"3BR-Compact" => "3BR-Compact",
			"3BR-Premium" => "3BR-Premium",
			"3BR-Deluxe" => "3BR-Deluxe",
			"3BR-Dual-Key" => "3BR-Dual-Key",
			"3BR+Study" => "3BR+Study",
			"3BR+Study-SOHO" => "3BR+Study-SOHO",
			"3BR-Loft" => "3BR-Loft",
			"3BR-Duplex" => "3BR-Duplex",
			"3BR-Duplex+Study" => "3BR-Duplex+Study",
			"3BR+Guest" => "3BR+Guest",
			"3BR+Flexi" => "3BR+Flexi",
			"3BR+Study-Maisonette" => "3BR+Study-Maisonette",
			"3BR+Study-SkySuite" => "3BR+Study-SkySuite",
			"3BR+Utility" => "3BR+Utility",
			"3BR+Yard" => "3BR+Yard",
			"3BR-Premium+Flexi" => "3BR-Premium+Flexi",
			"3BR+PES" => "3BR+PES",
			"3BR+RoofTerrace" => "3BR+RoofTerrace",
			"3BR+HobbyLoft" => "3BR+HobbyLoft",
			"3BR+Study+HobbyLoft" => "3BR+Study+HobbyLoft",
			"4BR" => "4BR",
			"4BR-Premium" => "4BR-Premium",
			"4BR-Deluxe" => "4BR-Deluxe",
			"4BR-Dual-Key" => "4BR-Dual-Key+Study",
			"4BR-Dual-Key+Study" => "4BR-Dual-Key+Study",
			"4BR-Dual-Key+RoofTerrace" => "4BR-Dual-Key+RoofTerrace",
			"4BR+Study" => "4BR+Study",
			"4BR+Study-SkySuite" => "4BR+Study-SkySuite",
			"4BR-Duplex" => "4BR-Duplex",
			"4BR-Duplex+Study" => "4BR-Duplex+Study",
			"4BR+Family" => "4BR+Family",
			"4BR+Studio" => "4BR+Studio",
			"4BR-Double-Volume" => "4BR-Double-Volume",
			"4BR-Suite" => "4BR-Suite",
			"4BR-Triplex" => "4BR-Triplex",
			"4BR-Grandeur" => "4BR-Grandeur",
			"4BR+PES" => "4BR+PES",
			"4BR+RoofTerrace" => "4BR+RoofTerrace",
			"4BR+Study+HobbyLoft" => "4BR+Study+HobbyLoft",
			"5BR" => "5BR",
			"5BR-Premium" => "5BR-Premium",
			"5BR+Study" => "5BR+Study",
			"5BR-Dual-Key+Study" => "5BR-Dual-Key+Study",
			"5BR+Family" => "5BR+Family",
			"5BR-Suite" => "5BR-Suite",
			"5BR+PES" => "5BR+PES",
			"5BR+HobbyLoft" => "5BR+HobbyLoft",
			"5BR+Study+HobbyLoft" => "5BR+Study+HobbyLoft",
			"Retail" => "Retail",
			"F&B" => "F&B"

		];
		return $response;
	}
}






