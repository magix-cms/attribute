<?php
/**
 * Class plugins_attribute_public
 */
class plugins_attribute_public extends plugins_attribute_db
{
    /**
     * @var frontend_model_template $template
     * @var frontend_model_data $data
     * @var http_header $header
     * @var filter_sanitize $sanitize
     * @var frontend_model_mail $mail
     * @var frontend_model_setting $settings
     * @var frontend_model_domain $modelDomain
     * @var frontend_model_module $module
     */
    protected frontend_model_template $template;
    protected frontend_model_data $data;
    protected frontend_model_catalog $modelCatalog;

    /**
     * @var int $id
     */
    public int $id;
    public string $lang;

    /**
     * frontend_controller_home constructor.
     * @param frontend_model_template|null $t
     */
    public function __construct(frontend_model_template $t = null)
    {
        $this->template = $t instanceof frontend_model_template ? $t : new frontend_model_template();
        $formClean = new form_inputEscape();
        $this->data = new frontend_model_data($this, $this->template);
        $this->modelCatalog = new frontend_model_catalog($this->template);

        if (http_request::isGet('id')) $this->id = $formClean->numeric($_GET['id']);
    }

    /**
     * Assign data to the defined variable or return the data
     * @param string $type
     * @param string|int|null $id
     * @param string|null $context
     * @param boolean $assign
     * @return mixed
     */
    private function getItems(string $type, $id = null, string $context = null, $assign = true) {
        return $this->data->getItems($type, $id, $context, $assign);
    }
    /**
     * Update data
     * @param $data
     * @throws Exception
     */
    private function add($data)
    {
        switch ($data['type']) {
            case 'cartpay':
                parent::insert(
                    array(
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
    private function setItemAttr($row)
    {
        $data = array();
        if ($row != null) {
            $data['id'] = $row['id_attr'];
            $data['name'] = $row['type_attr'];
            $data['iso'] = $row['iso_lang'];
        }
        return $data;
    }
    /**
     * @return array|null
     */
    public function getBuildAttr($id){

        $collection = $this->getItems('langAttrByCat',array('iso'=> $this->template->lang,'id'=> $id) ,'all',false);

        if($collection != null) {
            $newarr = [];
            foreach ($collection as &$item) {
                $newarr[] = $this->setItemAttr($item);
            }
            return $newarr;
        }else{
            return null;
        }
    }
    /**
     * @param $row
     * @return array
     */
    private function setItemValue($row)
    {
        $data = [];
        if (!empty($row)) {
            $data['id_type'] = $row['id_attr'];
            //$data['id_product'] = $row['id_product'];
            $data['type'] = $row['type_attr'];
            $data['id'] = $row['id_attr_va'];
            $data['price'] = $row['price_p'];
            $data['name'] = $row['value_attr'];
            $data['iso'] = $row['iso_lang'];
        }
        return $data;
    }

    /**
     * @return array|null
     */
    public function getBuildValue($id){

        $collection = $this->getItems('langValueByProduct', ['iso'=> $this->template->lang,'id'=> $id],'all',false);

        if($collection != null) {
            $newarr = [];
            foreach ($collection as &$item) {
                $newarr[$item['id_attr']][] = $this->setItemValue($item);
            }
            return $newarr;
        }
        else{
            return null;
        }
    }
    /**
     * @param $row
     * @return array
     */
    private function setItemData($row)
    {
        $data = array();
        if ($row != null) {
            $data['id'] = $row['id_attr'];
            $data['name'] = $row['type_attr'];
            $collection = $this->getItems('langValueByCat',array('iso'=> $this->template->lang,'id'=> $row['id_attr']) ,'all',false);
            if($collection != null) {
                foreach ($collection as $key => $item) {
                    $data['item'][$key]['id'] = $item['id_attr_va'];
                    $data['item'][$key]['value'] = $item['value_attr'];
                }
            }else{
                return null;
            }
            $data['iso'] = $row['iso_lang'];

        }
        return $data;
    }
    /**
     * @return array|null
     */
    public function getBuildData($id = null){
        if($id != null) {
            $collection = $this->getItems('langAttrByCat', array('iso' => $this->template->lang, 'id' => $id), 'all', false);
        }
        else{
            $collection = $this->getItems('langAttr', array('iso' => $this->template->lang), 'all', false);
        }
        $newarr = [];
        if($collection != null) {
            foreach ($collection as $item) {
                $newarr[] = $this->setItemData($item);
            }
        }
        else{
            return null;
        }
        return $newarr;
    }

    /**
     * @return array
     * @throws Exception
     */
    public function getBuildProductList(){
        $db_catalog = new frontend_db_catalog();
        $conditions = ' WHERE lang.iso_lang = :iso
						AND pc.published_p = 1 
						AND (img.default_img = 1 OR img.default_img IS NULL) 
						AND catalog.default_c = 1 
						AND catalog.id_product IN (SELECT id_product FROM mc_catalog WHERE id_cat = :id_cat) 
						ORDER BY catalog.order_p ASC';
        $collection = $db_catalog->fetchData(
            array('context' => 'all', 'type' => 'product', 'conditions' => $conditions),
            array('iso' => $this->template->lang,'id_cat' => $this->id)
        );
        if($collection != null) {
            // Retourne les id des produits
            foreach ($collection as $item) {
                $newItems[] = $item['id_product'];
            }
            $product_id = implode(",", $newItems);
            // Liste les attributs disponible avec id_product
            $attributes = $this->getItems('langValueInProduct', ['iso' => $this->template->lang, 'id' => $product_id], 'all', false);
            // crée un tableau sur base de l'id produit
            $newAttr = [];
            foreach ($attributes as $item) {
                $newAttr[$item['id_product']][$item['type_attr']][] = $this->setItemValue($item);
            }
            // Ajoute les attributes au produit si disponible
            foreach ($collection as &$row) {
                $row['attributes'] = $newAttr[$row['id_product']];
            }
        }
        $newarr = array();
        foreach ($collection as $item) {
            $newarr[] = $this->modelCatalog->setItemData($item,null,['attributes'=>'attributes']);
        }
        return $newarr;
    }

    /**
     * @param $collection
     * @throws Exception
     */
    public function getBuildAttribute($collection){
        if($collection != null) {
            // Retourne les id des produits
            $newItems = [];
            foreach ($collection as $item) {
                $newItems[] = $item['id_product'];
            }
            $product_id = implode(",", $newItems);
            // Liste les attributs disponible avec id_product
            $attributes = $this->getItems('langValueInProduct', ['iso' => $this->template->lang, 'id' => $product_id], 'all', false);
            // crée un tableau sur base de l'id produit
            $newAttr = [];
            foreach ($attributes as $item) {
                $newAttr[$item['id_product']][$item['type_attr']][] = $this->setItemValue($item);
            }
            // Ajoute les attributes au produit si disponible
            foreach ($collection as &$row) {
                $row['attributes'] = $newAttr[$row['id_product']];
            }
        }
        $newarr = array();
        foreach ($collection as $item) {
            $newarr[] = $this->modelCatalog->setItemData($item,null,['attributes'=>'attributes']);
        }
        return $newarr;
    }
    /**
     * @return array|null
     * @throws Exception
     */
    public function getBuildProductItems()
    {
        $db_catalog = new frontend_db_catalog();

        $collection = $db_catalog->fetchData(
            ['context' => 'one', 'type' => 'product'],
            ['iso' => $this->template->lang,'id' => $this->id]
        );
        // Ajoute les attributes au produit si disponible
        $collection['attributes'] = $this->getBuildValue($this->id);

        $imgCollection = $db_catalog->fetchData(
            ['context' => 'all', 'type' => 'images'],
            ['iso' => $this->template->lang,'id' => $this->id]
        );
        $associatedCollection = $db_catalog->fetchData(
            ['context' => 'all', 'type' => 'similar'],
            ['iso' => $this->template->lang,'id' => $this->id]
        );
        $newAttr = [];
        $newItems = [];
        if($associatedCollection != null) {
            // Retourne les id des produits
            foreach($associatedCollection as $item){
                $newItems[] = $item['id_product'];
            }
            $product_id = implode(",", $newItems);
            // Liste les attributs disponible avec id_product
            $attributes = $this->getItems('langValueInProduct', ['iso' => $this->template->lang, 'id' => $product_id], 'all', false);
            // crée un tableau sur base de l'id produit
            foreach ($attributes as $item) {
                $newAttr[$item['id_product']][$item['type_attr']][] = $this->setItemValue($item);
            }
            //print_r($newAttr);
            // Ajoute les attributes au produit si disponible
            foreach ($associatedCollection as &$row) {
                $row['attributes'] = $newAttr[$row['id_product']];
            }
        }
        if ($imgCollection != null) {
            $collection['img'] = $imgCollection;
        }

        if ($associatedCollection != null) {
            $collection['associated'] = $associatedCollection;
        }

        return $this->modelCatalog->setItemData($collection, null,['attributes'=>'attributes']);
    }
    /**
     * @param array $rawData
     * @return array
     */
    public function setAttributesData(array $rawData): array {
        $data = [];

        if (!empty($rawData)) {
            foreach ($rawData as $key => $value) {

                $data[$value['id_attr']][$key]['id_type'] = $value['id_attr'];
                $data[$value['id_attr']][$key]['type'] = $value['type_attr'];
                $data[$value['id_attr']][$key]['id'] = $value['id_attr_va'];
                $data[$value['id_attr']][$key]['price'] = $value['price_p'];
                $data[$value['id_attr']][$key]['name'] = $value['value_attr'];
                $data[$value['id_attr']][$key]['iso'] = $value['iso_lang'];
            }
        }

        return $data;
    }
    /**
     * @param array $data
     * @return array
     */
    public function extendProduct(array $data): array {
        return $this->setAttributesData($data);
    }
    /**
     * @param array $data
     * @return array
     */
    public function extendProductData(array $data) : array{
        $extend['newRow'] = ['attributes' => 'attributes'];
        $extend['collection'] = 'attributes';
        if (http_request::isGet('id')) $this->id = form_inputEscape::numeric($_GET['id']);
        if(!empty($data)) {
            $collection = $this->getItems('langValueByProduct', ['iso'=> $this->template->lang,'id' => $this->id ?? $data['id_product']],'all',false);

            //$videoCollection = $this->getitems('videos',['id' => $this->id ?? $data['id_product']],'all',false);
            $product = $this->extendProduct($collection);
            $extend['data'] = empty($product) ? [] : $product;
        }
        return $extend;
    }
    /**
     * @return string
     */
    public function add_to_cart_params(): string {
        $this->template->assign('attributes',$this->getBuildValue($this->id));
        return $this->template->fetch('attribute/add-to-cart.tpl');
    }

    /**
     * @param $params
     * @return int
     */
    public function replace_unit_price($params){
        // Retourne le prix venant de l'attribut ou venant du produit si aucun attribut
		$attribute = $params['param']['attribute'];
		$id_attr = is_array($attribute) ? end($attribute) : $attribute;
        $priceAttr = $this->getItems('priceByProduct',
            array('id' => $id_attr, 'id_product' => $params['id_product']), 'one', false);

        if($priceAttr['price_p'] != NULL){
            $unit_price = $priceAttr['price_p'];//10;
        }else{
            $pPrice = $this->getItems('product_price',$params['id_product'],'one',false);
            $unit_price = $pPrice['price_p'];
        }
        return $unit_price;
    }

    /**
     * @param array $params
     * @return array
     */
    public function get_param_value(array $params): array {
        $attributesValues = [];
        $logger = new debug_logger(MP_LOG_DIR);

        foreach($params['value'] as $id_attr => $id_attr_p) {
            $attr = $this->getItems('paramValue', ['id' => $id_attr_p, 'iso' => $this->template->lang], 'one', false);
            $attributesValues[$id_attr] = $attr['type_attr'].':&nbsp;'.$attr['value_attr'];

            $cartpay = $this->getItems('cartpay', ['id' => $params['items'], 'id_attr_va' => $attr['id_attr_va']], 'one', false);
            //$logger->tracelog(json_encode($cartpay));
            if(empty($cartpay)) {
                $this->add([
                    'type' => 'cartpay',
                    'data' => [
                        'id_items' => $params['items'],
                        'id_attr_va' => $attr['id_attr_va']
                    ]
                ]);
            }
        }
        return $attributesValues;
    }

    /**
     * @param int $id
     * @return array
     */
    public function getRetreiveParam(int $id) :array{
        $cartpay = $this->getItems('cartpay', ['id' => $id], 'all', false);
        $data = [];
        $i = 1;
        foreach($cartpay as $key => $item){
            $data['attribute'][$i] = $item['id_attr_va'];
            $i++;
        }
        return $data;
    }
}