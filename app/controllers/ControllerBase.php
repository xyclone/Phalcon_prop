<?php
#namespace Property\Controllers;

use Phalcon\Mvc\Controller;
use Phalcon\Mvc\Dispatcher;
use Property\Library\AclAction;
use Property\Helpers\Helpers;

class ControllerBase extends Controller
{
    public $images_url = PUBLIC_PATH;
    public static $ajax_modal = 
    '<div class="modal fade" id="modal-ajax-handler" tabindex="-1" role="dialog" aria-labelledby="ModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
        <div class="vertical-alignment-helper">
            <div id="dialog-box" class="modal-dialog vertical-align-center">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="btn btn-xs pull-right" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times fa-fw text-info" aria-hidden="true"></i></button>
                        <h4 class="modal-title"></h4>
                    </div>
                    <div class="modal-body">
                    </div>
                    <div class="modal-footer bg-default">
                        <button type="button" id="btnClose" class="btn btn-default" data-dismiss="modal"><i class="fa fa-times-circle-o fa-fw" aria-hidden="true"></i> Close</button>
                    </div>
                </div>
            </div>
        </div>
    </div>';
    
    /**
     * [truncate description]
     * @param  [type] $str   [description]
     * @param  [type] $width [description]
     * @return [type]        [description]
     */
    protected function str_truncate($str, $width) {
        return strtok(wordwrap($str, $width, "...\n"), "\n");
    }

    protected function initialize()
    {
        $this->tag->prependTitle('All New Property');
        if (empty($this->session->get('user')['username'])) {
            return $this->response->redirect('login');
        } 
        $this->view->baseUrl = BASE_URL;
        $this->view->ajax_modal = self::$ajax_modal;

    }

    protected function redirectBack()
    {
        return $this->response->redirect($this->request->getServer('HTTP_REFERER'));
    }    

    protected function forward($uri)
    {
        $uriParts = explode('/', $uri);
        $params = array_slice($uriParts, 2);
        return $this->dispatcher->forward(
            array(
                'controller' => $uriParts[0],
                'action' => $uriParts[1],
                'params' => $params
            )
        );
    }

    /**
     * [detectDelimiter description]
     * @param  [type] $csvFile [description]
     * @return [type]          [description]
     */ //echo '<pre>'; var_dump($post); echo '</pre>'; die();
    protected function detectDelimiter($csvFile)
    {
        $delimiters = array(';' => 0,',' => 0,"\t" => 0,"|" => 0);
        $handle = fopen($csvFile, "r");
        $firstLine = fgets($handle);
        fclose($handle); 
        foreach ($delimiters as $delimiter => &$count) {
            $count = count(str_getcsv($firstLine, $delimiter));
        }
        return array_search(max($delimiters), $delimiters);
    }

    /**
     * [replace_key description]
     * @param  [type] $arr    [description]
     * @param  [type] $oldkey [description]
     * @param  [type] $newkey [description]
     * @return [type]         [description]
     */
    protected function replace_key($arr, $oldkey, $newkey) {
        if(array_key_exists( $oldkey, $arr)) {
            $keys = array_keys($arr);
            $keys[array_search($oldkey, $keys)] = $newkey;
            return array_combine($keys, $arr);
        }
        return $arr;    
    }

    /**
     * [fileBaseName description]
     * @param  [type] $urlpath [description]
     * @return [type]          [description]
     */ //echo '<pre>'; var_dump($results); echo '</pre>'; die();
    public function fileBaseName($urlpath) {
        $basename = preg_replace('/^.+[\\\\\\/]/', '', $urlpath);
        return $basename;
    }


    /**
     * [fileUrlLink description]
     * @param  [type] $urlpath [description]
     * @return [type]          [description]
     */ //echo '<pre>'; var_dump($results); echo '</pre>'; die();
    public function fileUrlLink($urlpath, $folder=null) {
        $basename = preg_replace('/^.+[\\\\\\/]/', '', $urlpath);
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
        $host =  $this->images_url.'/images/'; //$protocol.DefaultClass::$projectImgUrl;
        $imgUrl = $host.$folder;
        return $imgUrl.'/'.$basename;
    }
}
