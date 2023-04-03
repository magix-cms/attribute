<?php
/*
 # -- BEGIN LICENSE BLOCK ----------------------------------
 #
 # This file is part of MAGIX CMS.
 # MAGIX CMS, The content management system optimized for users
 # Copyright (C) 2008 - 2019 magix-cms.com <support@magix-cms.com>
 #
 # OFFICIAL TEAM :
 #
 #   * Gerits Aurelien (Author - Developer) <aurelien@magix-cms.com> <contact@aurelien-gerits.be>
 #
 # Redistributions of files must retain the above copyright notice.
 # This program is free software: you can redistribute it and/or modify
 # it under the terms of the GNU General Public License as published by
 # the Free Software Foundation, either version 3 of the License, or
 # (at your option) any later version.
 #
 # This program is distributed in the hope that it will be useful,
 # but WITHOUT ANY WARRANTY; without even the implied warranty of
 # MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 # GNU General Public License for more details.

 # You should have received a copy of the GNU General Public License
 # along with this program.  If not, see <http://www.gnu.org/licenses/>.
 #
 # -- END LICENSE BLOCK -----------------------------------

 # DISCLAIMER

 # Do not edit or add to this file if you wish to upgrade MAGIX CMS to newer
 # versions in the future. If you wish to customize MAGIX CMS for your
 # needs please refer to http://www.magix-cms.com for more information.
 */
include_once ('db.php');
class plugins_attribute_core extends plugins_attribute_db{

    protected $template,$modelPlugins,$message,$arrayTools,$data, $modelLanguage, $collectionLanguage;
    public $controller,$plugins,$plugin,$attr_id,$edit,$id_pages;

    public function __construct($t = null)
    {
        $this->template = $t ? $t : new backend_model_template();
        $this->modelPlugins = new backend_model_plugins();
        $this->plugins = new backend_controller_plugins();
        $formClean = new form_inputEscape();
        $this->message = new component_core_message($this->template);
        $this->arrayTools = new collections_ArrayTools();
        $this->data = new backend_model_data($this);
        $this->modelLanguage = new backend_model_language($this->template);
        $this->collectionLanguage = new component_collections_language();
        if(http_request::isGet('controller')) {
            $this->controller = $formClean->simpleClean($_GET['controller']);
        }
        if(http_request::isGet('plugin')){
            $this->plugin = $formClean->simpleClean($_GET['plugin']);
        }
        // --- ADD or EDIT
        if (http_request::isGet('edit')) $this->edit = $formClean->numeric($_GET['edit']);
        if (http_request::isGet('id')) $this->id_pages = $formClean->simpleClean($_GET['id']);
        elseif (http_request::isPost('id')) $this->id_pages = $formClean->simpleClean($_POST['id']);
    }
    /**
     * Assign data to the defined variable or return the data
     * @param string $type
     * @param string|int|null $id
     * @param string $context
     * @param boolean $assign
     * @param boolean $pagination
     * @return mixed
     */
    private function getItems($type, $id = null, $context = null, $assign = true, $pagination = false) {
        return $this->data->getItems($type, $id, $context, $assign, $pagination);
    }

    /**
     * Method to override the name of the plugin in the admin menu
     * @return string
     */
    public function getExtensionName()
    {
        return $this->template->getConfigVars('attribute_plugin');
    }
    /**
     * @return mixed
     * @throws Exception
     */
    public function getAttrTree(){
        $this->modelLanguage->getLanguage();
        $defaultLanguage = $this->collectionLanguage->fetchData(array('context'=>'one','type'=>'default'));
        $list = $this->getItems('setPagesTree',array('default_lang'=>$defaultLanguage['id_lang']),'all',false,false);
        return $list;
    }
    /**
     * Update data
     * @param $data
     * @throws Exception
     */
    private function add($data)
    {
        switch ($data['type']) {
            case 'product':
                parent::insert(
                    array(
                        'context' => $data['context'],
                        'type' => $data['type']
                    ),
                    $data['data']
                );
                break;
        }
    }

    /**
     * Mise a jour des données
     * @param $data
     * @throws Exception
     */
    private function upd($data)
    {
        switch ($data['type']) {
            case 'product':
                parent::update(
                    array(
                        'context' => $data['context'],
                        'type' => $data['type']
                    ),
                    $data['data']
                );
                break;
        }
    }
    /**
     * @param $row
     * @return array
     */
    private function setItemValue($row)
    {
        $data = array();
        if ($row != null) {
            $data['id'] = $row['id_attr_p'];
            //$data['id_product'] = $row['id_product'];
            $data['type'] = $row['type_attr'];
            $data['name'] = $row['value_attr'];
            $data['iso'] = $row['iso_lang'];
            $data['price'] = $row['price_p'];
        }
        return $data;
    }

    /**
     * @return array|null
     */
    public function getBuildValue($id){

        $collection = $this->getItems('langValueByProduct',
            array('iso'=> $this->template->lang,'id'=> $id) ,'all',false);

        if($collection != null) {
            $newarr = array();
            foreach ($collection as &$item) {
                $newarr[] = $this->setItemValue($item);
            }
            return $newarr;
        }else{
            return null;
        }
    }
    /**
     * Execution du plugin dans un ou plusieurs modules core
     */
    public function run(){
        if(isset($this->controller)){
            switch($this->controller){
                case 'product':
                    $data = $this->getAttrTree();
                    $this->template->assign('attr',$data);
                    $this->template->assign('attrvalue',$this->getBuildValue($this->edit));
                    // ---- languages
                    //$this->getItems('immo',array('id'=>$this->edit),'all');
                    $this->modelPlugins->display('form/link-product.tpl');
                    break;

            }
        }
    }
}
?>