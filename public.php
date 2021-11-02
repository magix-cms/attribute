<?php
/**
 * Class plugins_attribute_public
 */
class plugins_attribute_public extends plugins_attribute_db
{
    /**
     * @var object
     */
    protected $template, $data, $modelCatalog;

    /**
     * @var int $id
     */
    protected $id;

    /**
     * frontend_controller_home constructor.
     * @param stdClass $t
     */
    public function __construct($t = null)
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
     * @param string $context
     * @param boolean $assign
     * @return mixed
     */
    private function getItems($type, $id = null, $context = null, $assign = true)
    {
        return $this->data->getItems($type, $id, $context, $assign);
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
            $newarr = array();
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
        $data = array();
        if ($row != null) {
            $data['id'] = $row['id_attr_va'];
            //$data['id_product'] = $row['id_product'];
            $data['type'] = $row['type_attr'];
            $data['name'] = $row['value_attr'];
            $data['iso'] = $row['iso_lang'];
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
        }else{
            $collection = $this->getItems('langAttr', array('iso' => $this->template->lang), 'all', false);
        }
        if($collection != null) {
            $newarr = array();
            foreach ($collection as $item) {
                $newarr[] = $this->setItemData($item);
            }
        }else{
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
            $attributes = $this->getItems('langValueInProduct',
                array('iso' => $this->template->lang, 'id' => $product_id), 'all', false);
            // crée un tableau sur base de l'id produit
            foreach ($attributes as $item) {
                $newAttr[$item['id_product']][] = $this->setItemValue($item);
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
            foreach ($collection as $item) {
                $newItems[] = $item['id_product'];
            }
            $product_id = implode(",", $newItems);
            // Liste les attributs disponible avec id_product
            $attributes = $this->getItems('langValueInProduct',
                array('iso' => $this->template->lang, 'id' => $product_id), 'all', false);
            // crée un tableau sur base de l'id produit
            foreach ($attributes as $item) {
                $newAttr[$item['id_product']][] = $this->setItemValue($item);
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
            array('context' => 'one', 'type' => 'product'),
            array('iso' => $this->template->lang,'id' => $this->id)
        );
        // Ajoute les attributes au produit si disponible
        $collection['attributes'] = $this->getBuildValue($this->id);

        $imgCollection = $db_catalog->fetchData(
            array('context' => 'all', 'type' => 'images'),
            array('iso' => $this->template->lang,'id' => $this->id)
        );
        $associatedCollection = $db_catalog->fetchData(
                array('context' => 'all', 'type' => 'similar'),
                array('iso' => $this->template->lang,'id' => $this->id)
            );
        $newAttr = array();
        $newItems = array();
        if($associatedCollection != null) {
            // Retourne les id des produits
            foreach($associatedCollection as $item){
                $newItems[] = $item['id_product'];
            }
            $product_id = implode(",", $newItems);
            // Liste les attributs disponible avec id_product
            $attributes = $this->getItems('langValueInProduct',
                array('iso' => $this->template->lang, 'id' => $product_id), 'all', false);
            // crée un tableau sur base de l'id produit
            foreach ($attributes as $item) {
                $newAttr[$item['id_product']][] = $this->setItemValue($item);
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
     * @param $params
     * @return int
     */
    public function impact_unit_price($params){
        // Retourne le prix venant de l'attribut ou venant du produit si aucun attribut
        $id_attr = $params['param']['attribute'];
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
     * @param $params
     * @return string
     */
    public function impact_param_value($params){
        $attr = $this->getItems('paramValue',
            array('id' => $params, 'iso' => $this->template->lang), 'one', false);
        return $attr['type_attr'].':&nbsp;'.$attr['value_attr'];
    }
}