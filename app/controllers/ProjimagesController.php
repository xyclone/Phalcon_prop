<?php

//Core
use Phalcon\DI;
use Phalcon\DI\FactoryDefault;
use Phalcon\Http\Response;
use Phalcon\Mvc\Model;
use Phalcon\Mvc\Model\Resultset;
use Phalcon\Mvc\View;
use Phalcon\Image\Adapter\Imagick;
use Property\Helpers\Helpers;
use Property\Library\DataTable;
use Property\Classes\PropertyClass;
use Property\Classes\UploadClass;
//Models
use Property\Models\Uploads;
use Property\Models\Projects;
use Property\Models\ProjectTypes;
use Property\Models\ProjectPropTypes;
use Property\Models\PropertyAgencies;
use Property\Models\PropertyDistricts;
use Property\Models\PropertyStatus;
use Property\Models\PropertyTenures;
use Property\Models\PropertyTypes;
use Property\Models\PropertyUnits;
use Property\Models\MrtStations;
use Property\Forms\RepoForm;

class ProjimagesController extends ControllerBase
{
    public $images_dir = IMG_PATH;
    public function initialize()
    {
        parent::initialize();
    }

    /**
     * [indexAction description]
     * @return [type] [description]
     */
    public function indexAction()
    {
        $this->view->setVars([
            'tokenKey' => $this->security->getTokenKey(),
            'token' => $this->security->getToken(),
            'form' => new RepoForm(null, []),
            'form_name' => 'project_images',
            'link_action' => 'projimages/process',
            'link_repo' => 'projimages/imagesHtml',
            'link_delete' => 'projimages/deleteImage',
            'link_back_del' => 'projimages/index',
            'form_delete' => 'delete_image',
            ]);
    }

    /**
     * [processAction description]
     * @return [type] [description]
     */ // echo '<pre>'; var_dump(is_dir($this->images_dir)); echo '</pre>';
    public function processAction()
    {
        $this->view->disable();
        if (!$this->request->isPost())  return $this->redirectBack();
        $data = $this->request->getPost();
        $projid = $data['project_id']; $projtype = $data['project_type']; $projptype = $data['project_property_type'];
        $folder = str_replace(' ','_',$projid."_".$projtype."_".$projptype);
        $project_folder = $this->images_dir.'/'.$folder;

        if(!is_dir($project_folder)) {
            $oldmask = umask(0);
            mkdir($project_folder, 0777 ,true);
        }
        //(!is_dir(DATA_PATH)&&!is_writable(DATA_PATH)) ? '/tmp/'.'ads' : DATA_PATH.'ads';
 
        $inserted=0; $errorLogs=[];
        if ($this->request->hasFiles() == true) {
            foreach ($this->request->getUploadedFiles() as $file) {                              
                if ($file->getSize() > 0) {
                    $fileUploaded = $file->getName();
                    $fileName = $file->getTempName();           
                    if($file->moveTo($project_folder.'/'. $fileUploaded)) {
                        $inserted++;
                    } else {
                        $errorLogs[] = "Error image upload: $fileUploaded.";
                    }
                }
            }
        }
        if($inserted>0) {
            if(!empty($errorLogs)&&count($errorLogs)>0) (new AdminLogs)->addLog($this->session->get('user')['username'], 'Insert Project Images', implode( "<br>", $errorLogs ));
            $project_info = Projects::findFirst((int)$data['project_id']);
            $addUpload = new Uploads;
            $addUpload->type = $project_info->project_name."[images]";
            $addUpload->filename = $project_info->project_name."[images]";
            $addUpload->remarks = json_encode(['project'=>$project_info->project_name,'items'=>$inserted]);
            if ($addUpload->save() !== false) {
                $result = Helpers::notify('success', $inserted.' Item(s) successfully imported.');
                $result['projtype'] = $projtype;
                $result['projptype'] = $projptype;
                $result['projid'] = $projid;
            }
        } else {
            $result = Helpers::notify('error', 'Error uploading images.');
        }
        return json_encode($result);
    }

    /**
     * [imagesHtmlAction description]
     * @return [type] [description]
     */
    public function imagesHtmlAction()
    {
        $this->view->disable();
        if (!$this->request->get())  return $this->redirectBack();
        $data = $this->request->get();
        $projid = $data['projectId']; $projType = $data['projType']; $projPtype = $data['projPtype'];
        $folder = str_replace(' ','_',$projid."_".$projType."_".$projPtype);

        $images_dir = $this->images_dir.'/'.$folder.'/';
              
        $allowed_ext = implode(",", UploadClass::$allowed_ext);
        $images = (!empty($images_dir)) ? glob($images_dir . "*.{".$allowed_ext."}", GLOB_BRACE) : [];        
           
        $urlLink = ""; $responseHtml = "";

        $responseHtml .= "
        <style>
        .imgOpts { 
            display: inline-block;
            position: absolute;
            width: 70px;
            background-color: rgba(255,255,255,0.4);
            padding: 2px;
            overflow:hidden;
            z-index: 99;
        }
        .imgSizeInfo {
            display: inline-block;
            position: absolute;
            width: 90%;
            background-color: rgba(255,255,255,0.75);
            padding: 2px;
            overflow:hidden;
            z-index: 99;
            bottom: 0;
            margin: 0 5px 3px 0!important;
            padding-bottom: 2px;
        }
        .imgSizeInfo span {
            width: 100%;
            position: relative;
            text-align: right;
        }

		a.btnDelete {
			position: absolute!important;
			cursor: pointer!important; 
			float: right!important;
			z-index: 999!important;
			top:0;
		}

        .placeholder {
            margin-top: 15px!important;
        }
        .sourceValue {
            position: relative;
            display: inline-block;
            width: 5px;
            color: rgba(255,255,255,0.4);
            z-index: 1;
            float: right;
        }
        .copylink {
            width: 117px !important;
            margin-left: 3px;
            margin-bottom: 3px;
        }
        .thumbnail_new {
            background-color: black;
            width: 225px;
            height: 175px;
            display: inline-block;
            background-size: contain;
            background-position: center center;
            background-repeat: no-repeat;
        }

        #html5-watermark {
            position: absolute !important;
            top: auto !important;
            left: auto !important; 
            right: 10px !important;   
            bottom: 56px !important;
        }
        </style>
        ";
        
        if(count($images)>0 ) {
            $responseHtml .= "<div class='row'>";
            foreach ($images as $key => $image) {
                $ext = substr($image, strrpos($image, '.')+1);
                $imagethumbnail = $this->fileUrlLink($image, $folder);
                $basename = $this->fileBaseName($image);
                $dataAttr="";
                $imgInfo = UploadClass::getImageInfo2($this->fileUrlLink($image, $folder));
                if(empty($imgInfo['height']) && empty($imgInfo['width'])) {
                    $imgInfo = UploadClass::getImageInfo($this->fileUrlLink($image, $folder));
                }

                //&nbsp;<a href="#" data-toggle="modal" data-target="#modal-ajax-handler" data-action="Delete Image" data-id="'.$this->fileBaseName($image).'" data-name="'.$this->fileBaseName($image).'" class="btn btn-xs btn-danger"><i class="fa fa-trash-o fa-lg" aria-hidden="true"></i></a>
                //$responseHtml .= '<div class="imgSizeInfo">&nbsp;&nbsp;<span>'.$imgInfo['width'].'x'.$imgInfo['height'].' </span></div>';

                //Html5Lightbox
                $responseHtml .= '<div class="col-xs-12 col-sm-6 col-md-4 col-lg-3 placeholder">';
                	$responseHtml .= '&nbsp;<a href="#" data-toggle="modal" data-target="#modal-ajax-handler" data-action="Delete Image" data-id="'.$image.'" data-name="'.$this->fileBaseName($image).'" class="btn btn-xs btn-danger btnDelete"><i class="fa fa-trash-o fa-lg" aria-hidden="true"></i></a>';
                	$responseHtml .= '<div class="imgSizeInfo">&nbsp;<span>'.$basename.' </span></div>';
                		$responseHtml .= '<a href="'.$this->fileUrlLink($image, $folder).'" class="html5lightbox" data-group="mygroup"  data-thumbnail="'.$this->fileUrlLink($image, $folder).'" '.$dataAttr.'>';
                			$responseHtml .= '<div class="thumbnail_new img-responsive" style="background-image: url('.$imagethumbnail.');"></div>';
                		$responseHtml .= '</a>';
                $responseHtml .= '</div>';   
            }
            $responseHtml .= "</div>";
        } else {
            $responseHtml .= "<div class='row-fluid'><div class='col-sm-12 alert alert-warning'>No images attached to this project.</div></div>";
        }
        
        //Initialize Lightbox
        $responseHtml .= '<script>
            $(document).ready(function() {
                $(".html5lightbox").html5lightbox();
            });
        </script>';
        
        $this->response->setContent($responseHtml);
        return $this->response;
    }

    /**
     * [deleteImageAction description]
     * @return [type] [description]
     */ //echo '<pre>'; var_dump($results); echo '</pre>'; die();
    public function deleteImageAction() 
    {
    	$this->view->disable();
        if (!$this->request->isPost())  return $this->response->redirect('projimages');
        // /if (!$this->security->checkToken())  return json_encode(Helpers::notify('warning', 'Invalid token id.'));
        $data = $this->request->getPost();
        
        $images_path = trim($data['id']);
        $projtype = trim($data['projtype']);
        $projptype = trim($data['projptype']);
        $projid = trim($data['projid']);
        $basename = $this->fileBaseName($images_path);
        if (file_exists($images_path)) {
            unlink($images_path);
            if(!file_exists($images_path)) {
                $result = Helpers::notify('success', ' Image successfully deleted.');
                $result['projtype'] = $projtype;
                $result['projptype'] = $projptype;
                $result['projid'] = $projid;
                $result['close'] = 2;
            } else {
	            $result = Helpers::notify('error', 'Error deleting '.$basename.'.');
	        }
        }
        return json_encode($result);
    }

    /**
     * [getprojectsAction description]
     * @return [type] [description]
     */ //echo '<pre>'; var_dump($data); echo '</pre>'; die();
    public function getprojectsAction()
    {
        $this->view->disable();
        if (!$this->request->isPost())  return $this->response->redirect('projimages'); 
        $data = $this->request->getPost();
        $proj_options = [];
        $projects = Projects::find(["columns"=>"id,project_name","conditions"=>"project_type=?1 AND proj_property_type=?2","bind"=>[1=>$data['projtype'],2=>$data['projptype']]]);
        if($projects&&$projects->count()>0) {
            foreach($projects as $key => $field) {
                $proj_options[$key] = ['id'=>$field->id,'text'=>$field->project_name];
            }
        }
        return json_encode($proj_options);
    }
}