<?php
namespace Property\Classes;

class UploadClass
{
	public static $project_fields = [
		'project_type'=>'Project Type',
		'proj_property_type'=>'Project Property Type',
		'project_name'=>'Project Name',
		'old_project_name'=>'Old Project Name',
		'chinese_name'=>'Chinese Name',
		'all_number'=>'All Number',
		'street_name'=>'Street Name',
		'total_units'=>'Total Units',
		'total_available_units'=>'Available Total Units',

		'available_date'=>'Available Date',
		'detached_house'=>'Detached House',
		'semi_detached_house'=>'Semi-Detached House',
		'terrace_house'=>'Terrace House',
		'apt_condo'=>'Apt/Condo',
		'shops'=>'Shops',
		'childcare'=>'Childcare',

		'old_total_units'=>'Total Units Sold',
		'unit_type'=>'Unit Type',
		'available_unit_type'=>'Available Unit Type',
		'date_avail_unit_updated'=>'Date when Available Unit Type is updated',
		'property_type'=>'Property Type',
		'tenure'=>'Tenure',
		'top_year'=>'TOP Yr',
		'top_month'=>'TOP Mth',
		'top_date'=>'TOP Day',
		'district'=>'District',
		'planning_region'=>'Planning Region',
		'planning_area'=>'Planning Area',
		'median_psf'=>'Median psf',
		'low_psf'=>'Low psf',
		'high_psf'=>'High psf',
		'status'=>'Status',
		'status_date'=>'Date',
		'status2'=>'Status2',
		'status2_date'=>'Date2',
		'sale_price'=>'Sale Price in Millions',
		'psf_ppr'=>'psf ppr',
		'psm_ppr'=>'psm ppr',
		'gls_sold_date'=>'Enbloc or GLS Sold Date',
		'low_millions'=>'Low Millions',
		'high_millions'=>'High Millions',
		'tender_agency'=>'Tender Agency',
		'stb_application_date'=>'STB Appln Date',
		'stb_approval_date'=>'STB Approval Date',
		'completion_date'=>'Completion Date',
		'vacant_possession_date'=>'Vacant Possession Date',
		'successful_tenderer'=>'Successful tenderer',
		'no_transactions'=>'Number of Transactions',
		'transaction_month'=>'Transaction Month',
		'rental_low_psf_pm'=>'Rental Low psf pm',
		'rental_median_psf_pm'=>'Rental Median psf pm',
		'rental_high_psf_pm'=>'Rental High psf pm',
		'no_of_rentals'=>'Number of Rentals',
		'rental_period'=>'Rental Per Quarter',
		'rental_amount'=>'Rental Amount',
		'vacant_date'=>'Vacant date',
		'furnishing'=>'Furnishing',
		'original_top'=>'Original TOP',
		'mrt'=>'MRT LRT',
		'mrt_distance_km'=>'Distance km',
		'primary_school_within_1km'=>'Primary School within 1km',
		'highest_flr'=>'Highest Flr',
		'site_area_sqft'=>'Site Area sqft',
		'site_area_sqmt'=>'Site Area sqm',
		'plot_ratio'=>'Plot Ratio',
		'gfa_sqft'=>'GFA sqft',
		'gfa_sqmt'=>'GFA sqm',
		'non_residential_area_sqft'=>'Non Residential Area sqft',
		'non_residential_area_sqm'=>'Non Residential Area sqm',
		'office_sqft'=>'Office sqft',
		'office_sqm'=>'Office sqm',
		'retail_sqft'=>'Retail sqft',
		'retail_sqm'=>'Retail sqm',
		'factory_sqft'=>'Factory sqft',
		'factory_sqm'=>'Factory sqm',
		'warehouse_sqft'=>'Warehouse sqft',
		'warehouse_sqm'=>'Warehouse sqm',
		'developer'=>'Developer Owner',
		'marketing_agency'=>'Mkting Agency',
		'project_ref_no'=>'Project Ref Number',
		'approved_date'=>'Date Approved',
		'locality'=>'Locality',
		'top_no'=>'TOP Number',
		'issue_date'=>'Date Issued',
		'floor_area'=>'Floor Area',
		'cost'=>'Cost',
		'development_status'=>'Development Status',
		'ds_date'=>'DS Date',
		'land_type'=>'Land Type',
		'sector'=>'Sector',
		'date_updated'=>'Date Spreadsheet Updated',
		'description'=>'Description'
	];
	public static $project_per_project = [
		'project_name'=>'Project Name',
		'project_type'=>'Project Type',
		'proj_property_type'=>'Project Property Type',
		'area_sqmt'=>'Area sqm',
		'area_sqft'=>'Area sqft',
		'unit_type'=>'Unit Type',
		'low_price'=>'Low Price',
		'median_price'=>'Median Price',
		'high_price'=>'High Price',
		'no_of_units'=>'Number of units',
		'units_sold'=>'Units sold',
		'units_unsold'=>'Units unsold',
		'share_value'=>'Share Value',
		'share_amount'=>'Share Amt',
		'mtce_fee'=>'Mtce Fee',
	];
	public static $project_detail_fields = [
		'project_type'=>'Project Type',
		'proj_property_type'=>'Project Property Type',
		'project_id'=>'Project Name',
		'unit_type'=>'Unit Type',
		'address'=>'Address',
		'no_units_per_transaction'=>'Number of units per transaction',
		'area_type'=>'Type of Area',
		'transacted_price'=>'Transacted Price',
		'nett_price'=>'Nett Price',
		'unit_price_psm'=>'Unit Price psm',
		'unit_price_psf'=>'Unit Price psf',
		'sale_date'=>'Sale Date',
		'property_type'=>'Property Type',
		'property2_type'=>'Property Type 2',
		'tenure'=>'Tenure',
		'tenure2'=>'Tenure 2',
		'top_year'=>'TOP Yr',
		'type_of_sale_per_trxn'=>'Type of Sale Per Transaction',
		'hdb_pte'=>'HDB or Pte',
		'district'=>'District',
		'postal_sector'=>'Postal Sector',
		'postal_code'=>'Postal Code',
		'planning_region'=>'Planning Region',
		'planning_area'=>'Planning Area',
		'number'=>'Number',
		'street_name'=>'Street Name',
		'level'=>'Level',
		'stack'=>'Stack',
		'type'=>'Type',
		'area_sqm'=>'Area sqm',
		'area_sqf'=>'Area sqft',
		'built_area_per_sqft'=>'BuiltUp area sqft',
		'share_value'=>'Share Value',
	];

	public static $allowed_ext = ["jpg","jpeg","png"];

    /**
     * [getSelfHost description]
     * @return [type] [description]
     */
    public static function getSelfHost() {
        $response = "";
        $di = \Phalcon\DI::getDefault()->get('config');
        if(!empty($di->revive))
            $response = $di->revive->host;
        return $response;
    }

    /**
     * [getImageInfo description]
     * @param  [type] $img [description]
     * @return [type]      [description]
     */
    public static function getImageInfo($img) {
        ini_set("allow_url_fopen", 1);
        $response = [];
        list($width, $height, $type, $attr) = \getimagesize($img);
        $response["width"] = $width;
        $response["height"] = $height;
        if(empty($response["width"]) && empty($response["height"]))
            error_log("Error getting image dimension using getImageInfo()", 0);
        return $response;
    }

    /**
     * [getImageInfo2 description]
     * @param  [type] $img [description]
     * @return [type]      [description]
     */
    public static function getImageInfo2($img) {
        ini_set("allow_url_fopen", 1);
        $response = [];
        $data = \getimagesize($img);
        $response["width"] = $data[0];
        $response["height"] = $data[1];
        if(empty($response["width"]) && empty($response["height"]))
            error_log("Error getting image dimension using getImageInfo2()", 0);
        return $response;
    }

    /**
     * [getImageInfo3 description]
     * @param  [type] $img [description]
     * @return [type]      [description]
     */
    public static function getImageInfo3($img) {
        $response = [];
        $image = new FastImage($img);
        list($width, $height) = $image->getSize();
        $response["width"] = $width;
        $response["height"] = $height; 
        if(empty($response["width"]) && empty($response["height"]))
            error_log("Error getting image dimension using getImageInfo3()", 0);
        return $response;
    }

    /**
     * [getImageInfo4 description]
     * @param  [type] $img [description]
     * @return [type]      [description]
     */
    public static function getImageInfo4($img) {
        $response = ["width"=>"","height"=>""];
        $ch = curl_init();
        $timeout = 0; // set to zero for no timeout
        curl_setopt ($ch, CURLOPT_URL, $img);
        curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        $file_contents = curl_exec($ch);
        curl_close($ch);
        $new_image = ImageCreateFromString($file_contents);
        // Get new dimensions
        $response["width"] = imagesx($new_image);
        $response["height"] = imagesy($new_image); 
        if(empty($response["width"]) && empty($response["height"])) {
            $response = ["width"=>"","height"=>""];
            error_log("Error getting image dimension using getImageInfo4()", 0);
        }
        return $response;
    }

}
