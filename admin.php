<?php
class plugins_attribute_admin extends plugins_attribute_db
{
    public $edit, $action, $tabs, $search, $plugin, $controller;
    protected $message, $template, $header, $data, $modelLanguage, $collectionLanguage, $order, $upload, $config, $imagesComponent, $modelPlugins, $routingUrl, $makeFiles, $finder, $plugins;
    public $id_attr, $id_attr_va, $content, $pages, $img, $iso, $del_img, $ajax, $tableaction,
        $tableform, $offset, $name_img, $type_attr, $value_attr, $attrData, $attrValue, $cats_id, $id_attr_ca,$price_p;

    public $tableconfig = array(
        'all' => array(
            'id_attr',
            'type_attr' => array('title' => 'name'),
            'num_value' => array('title' => 'name','input' => null,'class' => ''),
            'date_register'
        )
    );
    /**
     * frontend_controller_home constructor.
     */
    public function __construct($t = null)
    {
        $this->template = $t ? $t : new backend_model_template;
        $this->message = new component_core_message($this->template);
        $this->header = new http_header();
        $this->data = new backend_model_data($this);
        $formClean = new form_inputEscape();
        $this->modelLanguage = new backend_model_language($this->template);
        $this->collectionLanguage = new component_collections_language();
        $this->modelPlugins = new backend_model_plugins();
        $this->routingUrl = new component_routing_url();
        $this->finder = new file_finder();
        // --- GET
        if (http_request::isGet('controller')) $this->controller = $formClean->simpleClean($_GET['controller']);
        if (http_request::isGet('edit')) $this->edit = $formClean->numeric($_GET['edit']);
        if (http_request::isGet('action')) $this->action = $formClean->simpleClean($_GET['action']);
        elseif (http_request::isPost('action')) $this->action = $formClean->simpleClean($_POST['action']);
        if (http_request::isGet('tabs')) $this->tabs = $formClean->simpleClean($_GET['tabs']);
        if (http_request::isGet('ajax')) $this->ajax = $formClean->simpleClean($_GET['ajax']);
        if (http_request::isGet('offset')) $this->offset = intval($formClean->simpleClean($_GET['offset']));

        if (http_request::isGet('tableaction')) {
            $this->tableaction = $formClean->simpleClean($_GET['tableaction']);
            $this->tableform = new backend_controller_tableform($this, $this->template);
        }

        // --- Search
        if (http_request::isGet('search')) {
            $this->search = $formClean->arrayClean($_GET['search']);
            $this->search = array_filter($this->search, function ($value) {
                return $value !== '';
            });
        }

        // --- ADD or EDIT
        if (http_request::isGet('id')) $this->id_attr = $formClean->simpleClean($_GET['id']);
        elseif (http_request::isPost('id')) $this->id_attr = $formClean->simpleClean($_POST['id']);
        if (http_request::isPost('attrData')) $this->attrData = $formClean->arrayClean($_POST['attrData']);
        if(http_request::isPost('attrValue')){
            $this->attrValue = $formClean->arrayClean($_POST['attrValue']);
        }
        if (http_request::isPost('attributes_id')) $this->attributes_id = $formClean->simpleClean($_POST['attributes_id']);
        if (http_request::isPost('id_attr_va')) $this->id_attr_va = $formClean->simpleClean($_POST['id_attr_va']);
        if (http_request::isPost('cats_id')) $this->cats_id = $formClean->simpleClean($_POST['cats_id']);
        if (http_request::isPost('id_attr_ca')) $this->id_attr_ca = $formClean->simpleClean($_POST['id_attr_ca']);
        if (http_request::isPost('price_p')) $this->price_p = $formClean->simpleClean($_POST['price_p']);
        // --- Recursive Actions
        if (http_request::isGet('attr')) $this->pages = $formClean->arrayClean($_GET['attr']);
        # ORDER PAGE
        if (http_request::isPost('attribute')) $this->order = $formClean->arrayClean($_POST['attribute']);
        if (http_request::isGet('plugin')) $this->plugin = $formClean->simpleClean($_GET['plugin']);
        # JSON LINK (TinyMCE)
        //if (http_request::isGet('iso')) $this->iso = $formClean->simpleClean($_GET['iso']);
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
     * @param $ajax
     * @return mixed
     * @throws Exception
     */
    public function tableSearch($ajax = false)
    {
        $this->modelLanguage->getLanguage();
        $defaultLanguage = $this->collectionLanguage->fetchData(array('context'=>'one','type'=>'default'));
        $results = $this->getItems('pages', array('default_lang' => $defaultLanguage['id_lang']), 'all',true,true);
        $params = array();

        if($ajax) {
            $params['section'] = 'pages';
            $params['idcolumn'] = 'id_attr';
            $params['activation'] = false;
            $params['sortable'] = false;
            $params['checkbox'] = true;
            $params['edit'] = true;
            $params['dlt'] = true;
            $params['readonly'] = array();
            $params['cClass'] = 'plugins_attribute_admin';
        }

        $this->data->getScheme(array('mc_attribute','mc_attribute_value'),array('id_attr','type_attr','value_attr','date_register'),$this->tableconfig['all']);


        return array(
            'data' => $results,
            'var' => 'pages',
            'tpl' => 'index.tpl',
            'params' => $params
        );
    }
    /**
     * Update data
     * @param $data
     * @throws Exception
     */
    private function add($data)
    {
        switch ($data['type']) {
            case 'page':
            case 'contentPage':
            case 'value':
            case 'contentValue':
            case 'cat':
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
            case 'page':
            case 'contentPage':
            case 'contentValue':
            case 'order':
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
     * Insertion de données
     * @param $data
     * @throws Exception
     */
    private function del($data)
    {
        switch($data['type']){
            case 'delPages':
            case 'delValue':
            case 'delCat':
            case 'delProduct':
                parent::delete(
                    array(
                        'type' => $data['type']
                    ),
                    $data['data']
                );
                $this->message->json_post_response(true,'delete',$data['data']);
                break;
        }
    }
    /**
     * @param $id
     * @return void
     * @throws Exception
     */
    private function saveContent($id)
    {

        foreach ($this->attrData as $lang => $content) {
            $content['id_lang'] = $lang;
            $content['id_attr'] = $id;
            $content['type_attr'] = (!empty($content['type_attr']) ? $content['type_attr'] : NULL);

            $contentPage = $this->getItems('contentPage', array('id_attr' => $id, 'id_lang' => $lang), 'one', false);

            if ($contentPage != null) {
                $this->upd(
                    array(
                        'type' => 'contentPage',
                        'data' => $content
                    )
                );
            } else {
                $this->add(
                    array(
                        'type' => 'contentPage',
                        'data' => $content
                    )
                );
            }
        }
        //$this->message->json_post_response(true, 'update', array('result'=>$id));
    }
    /**
     * @param $id
     * @return void
     * @throws Exception
     */
    private function saveValueContent($id)
    {

        foreach ($this->attrValue as $lang => $content) {
            $content['id_lang'] = $lang;
            $content['id_attr_va'] = $id;
            $content['value_attr'] = (!empty($content['value_attr']) ? $content['value_attr'] : NULL);

            $contentPage = $this->getItems('contentValue', array('id_attr_va' => $id, 'id_lang' => $lang), 'one', false);

            if ($contentPage != null) {
                $this->upd(
                    array(
                        'type' => 'contentValue',
                        'data' => $content
                    )
                );
            } else {
                $this->add(
                    array(
                        'type' => 'contentValue',
                        'data' => $content
                    )
                );
            }
        }
        //$this->message->json_post_response(true, 'update', array('result'=>$id));
    }
    /**
     * @param $data
     * @return array
     * @throws Exception
     */
    private function setItemData($data){
        $arr = [];
        foreach ($data as $page) {

            if (!array_key_exists($page['id_attr'], $arr)) {
                $arr[$page['id_attr']] = [];
                $arr[$page['id_attr']]['id_attr'] = $page['id_attr'];
                $arr[$page['id_attr']]['date_register'] = $page['date_register'];
            }
            $arr[$page['id_attr']]['content'][$page['id_lang']] = array(
                'id_lang'           => $page['id_lang'],
                'iso_lang'          => $page['iso_lang'],
                'type_attr'        => $page['type_attr']
            );
        }
        return $arr;
    }
    /**
     * @param $data
     * @return array
     * @throws Exception
     */
    private function setItemValueData($data){
        $arr = [];
        foreach ($data as $page) {
            if (!array_key_exists($page['id_attr_va'], $arr)) {
                $arr[$page['id_attr_va']] = [];
                $arr[$page['id_attr_va']]['id_attr_va'] = $page['id_attr_va'];
                if($page['default_lang'] = 1) {
                    $arr[$page['id_attr_va']]['value_attr'] = $page['value_attr'];
                }
            }
            $arr[$page['id_attr_va']]['content'][$page['id_lang']] = array(
                'id_lang'           => $page['id_lang'],
                'iso_lang'          => $page['iso_lang'],
                'value_attr'        => $page['value_attr']
            );
        }
        return $arr;
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
        }
        return $data;
    }

    /**
     * @param $params
     * @return mixed
     */
    public function getProductData($params){
        return $this->getItems('cartpay_product', $params, 'all',false);
    }
    public function getSchemeSale(){}
    public function getSchemeQuotation(){}
    /**
     * @return mixed
     * @throws Exception
     */
    public function getCategory(){

        $defaultLanguage = $this->collectionLanguage->fetchData(array('context'=>'one','type'=>'default'));

        $list = $this->getItems('pagesPublishedSelect',array('default_lang'=> $defaultLanguage['id_lang']),'all',false);

        $lists = $this->data->setPagesTree($list,'cat');

        $this->template->assign('cats',$lists);
    }
    /**
     * @param $type
     */
    protected function order(){
        for ($i = 0; $i < count($this->order); $i++) {
            $this->upd(['type' => 'order', 'data' => ['id_attr_p' => $this->order[$i], 'order_attr_p' => $i]]);
        }
    }
    /**
     * @throws Exception
     */
    public function run()
    {
        if (isset($this->tableaction)) {
            $this->tableform->run();
        } elseif (isset($this->action)) {
            switch ($this->action) {
                case 'add':
                    if(isset($this->attrData)) {
                        $this->add(array(
                                'type' => 'page'/*,
                                'data' => array(
                                    'type_attr' => $this->attrData['type_attr']
                                )*/
                            )
                        );
                        $page = $this->getItems('root',null,'one',false);
                        if ($page['id_attr']) {
                            $this->saveContent($page['id_attr']);
                            $this->message->json_post_response(true,'add_redirect');
                        }
                        //$this->message->json_post_response(true, 'add');
                    }elseif(isset($this->attrValue)){

                        $this->add(array(
                                'type' => 'value',
                                'data' => array(
                                    'id_attr' => $this->id_attr
                                )
                            )
                        );
                        $page = $this->getItems('lastValue',array('id' => $this->id_attr),'one',false);
                        if ($page['id_attr_va']) {
                            $this->saveValueContent($page['id_attr_va']);

                            $defaultLanguage = $this->collectionLanguage->fetchData(array('context' => 'one', 'type' => 'default'));
                            $this->getItems('lastLangValue', array('id' => $this->id_attr, 'default_lang' => $defaultLanguage['id_lang']), 'one', 'row');

                            $display = $this->template->fetch('loop/value.tpl');
                            $this->message->json_post_response(true, 'add', $display);
                        }
                    }elseif(isset($this->cats_id)){

                        $this->add(array(
                                'type' => 'cat',
                                'data' => array(
                                    'id_attr' => $this->id_attr,
                                    'id_cat' => $this->cats_id
                                )
                            )
                        );
                        $defaultLanguage = $this->collectionLanguage->fetchData(array('context' => 'one', 'type' => 'default'));
                        $this->getItems('lastCat', array('default_lang' => $defaultLanguage['id_lang']), 'one', 'row');

                        $display = $this->template->fetch('loop/category.tpl');
                        $this->message->json_post_response(true, 'add', $display);

                    }elseif(isset($this->attributes_id)){

                        $this->add(array(
                                'type' => 'product',
                                'data' => array(
                                    'id_attr_va' => $this->attributes_id,
                                    'id_product' => $this->id_attr,
                                    'price_p' => (!empty($this->price_p)) ? number_format(str_replace(",", ".", $this->price_p), 4, '.', '') : NULL
                                )
                            )
                        );
                        $defaultLanguage = $this->collectionLanguage->fetchData(array('context' => 'one', 'type' => 'default'));
                        $lastProduct = $this->getItems('lastProduct', array('default_lang' => $defaultLanguage['id_lang']), 'one', false);
                        $this->template->assign('row',$this->setItemValue($lastProduct));
                        $display = $this->template->fetch('loop/attribute.tpl');
                        $this->message->json_post_response(true, 'add', $display);

                    }else{
                        $this->modelLanguage->getLanguage();
                        $this->template->display('add.tpl');
                    }
                    break;
                case 'edit':
                    if(isset($this->attrValue)){
                        $this->saveValueContent($this->id_attr_va);
                        /*$this->upd(array(
                                'type' => 'page',
                                'data' => array(
                                    'url_book' => $this->oddData['url_book'],
                                    'id' => $this->id_book
                                )
                            )
                        );*/
                        $this->message->json_post_response(true, 'update');
                    }elseif(isset($this->attrData)){
                        $this->saveContent($this->id_attr);
                        $this->message->json_post_response(true, 'update');
                    }else{
                        $this->modelLanguage->getLanguage();
                        $setEditData = $this->getItems('page', array('edit'=>$this->edit),'all',false);
                        $setEditData = $this->setItemData($setEditData);
                        $this->template->assign('page',$setEditData[$this->edit]);

                        //$defaultLanguage = $this->collectionLanguage->fetchData(array('context'=>'one','type'=>'default'));
                        //$attrvalue = $this->getItems('attrvalue', array('edit'=>$this->edit,'default_lang' => $defaultLanguage['id_lang']),'all',false);
                        //$this->template->assign('attrvalue',$attrvalue);
                        $setEditData = $this->getItems('attrvalue', array('edit'=>$this->edit),'all',false);
                        $setEditData = $this->setItemValueData($setEditData);
                        $this->template->assign('attrvalue',$setEditData);
                        $defaultLanguage = $this->collectionLanguage->fetchData(array('context'=>'one','type'=>'default'));
                        $this->getItems('attrCats',array('default_lang'=>$defaultLanguage['id_lang'],'id'=>$this->edit),'all');
                        //$this->getItems('cats',array('default_lang'=>$defaultLanguage['id_lang']),'all');
                        $this->getCategory();
                        $this->template->display('edit.tpl');
                    }
                    break;
                case 'delete':
                    if(!empty($this->tabs)) {
                        switch ($this->tabs) {
                            case 'value':
                                $this->del(
                                    array(
                                        'type' => 'delValue',
                                        'data' => array(
                                            'id' => $this->id_attr
                                        )
                                    )
                                );
                                break;
                            case 'category':
                                $this->del(
                                    array(
                                        'type' => 'delCat',
                                        'data' => array(
                                            'id' => $this->id_attr
                                        )
                                    )
                                );
                                break;
                            case 'attribute':
                                $this->del(
                                    array(
                                        'type' => 'delProduct',
                                        'data' => array(
                                            'id' => $this->id_attr
                                        )
                                    )
                                );
                                break;
                        }
                    }else{
                        $this->del(
                            array(
                                'type' => 'delPages',
                                'data' => array(
                                    'id' => $this->id_attr
                                )
                            )
                        );
                    }
                    break;
                case 'order':
                    if (isset($this->order) && is_array($this->order)) {
                        $this->order();
                    }
                    break;
            }
        }else{
            $this->modelLanguage->getLanguage();
            $defaultLanguage = $this->collectionLanguage->fetchData(array('context'=>'one','type'=>'default'));
            $this->getItems('pages', array('default_lang' => $defaultLanguage['id_lang']), 'all',true,true);
            $this->data->getScheme(array('mc_attribute','mc_attribute_value'),array('id_attr','type_attr','value_attr','date_register'),$this->tableconfig['all']);
            $this->template->display('index.tpl');
        }
    }
}
?>