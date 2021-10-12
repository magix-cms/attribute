<?php
/**
 * Class plugins_teams_public
 */
class plugins_attribute_public extends plugins_attribute_db
{
    /**
     * @var object
     */
    protected $template, $data;

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
    private function setItemData($row)
    {
        $data = array();
        if ($row != null) {
            $data['id'] = $row['id_attr'];
            $data['name'] = $row['type_attr'];
            $collection = $this->getItems('langValueByCat',array('iso'=> $this->template->lang,'id'=> $row['id_attr']) ,'all',false);
            foreach($collection as $key => $item){
                $data['item'][$key]['id'] = $item['id_attr_va'];
                $data['item'][$key]['value'] = $item['value_attr'];
            }
            $data['iso'] = $row['iso_lang'];

        }
        return $data;
    }
    /**
     * @return array|null
     */
    public function getBuildValue($id = null){
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
     *
     */
    /*public function run(){
        $newarr = $this->getBuildValue();
        print '<pre>';
        print_r($newarr);
        print '<pre>';
    }*/
}