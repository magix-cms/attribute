<?php
class plugins_attribute_db
{
    /**
     * @param array $config
     * @param array $params
     * @return array|bool
     */
    public function fetchData(array $config, array $params = []) {
        if ($config['context'] === 'all') {
            switch ($config['type']) {
                case 'pages':
                    $limit = '';
                    if ($config['offset']) {
                        $limit = ' LIMIT 0, ' . $config['offset'];
                        if (isset($config['page']) && $config['page'] > 1) {
                            $limit = ' LIMIT ' . (($config['page'] - 1) * $config['offset']) . ', ' . $config['offset'];
                        }
                    }

                    $query = "SELECT p.id_attr, c.type_attr ,p.date_register, (SELECT count(id_attr_va) FROM mc_attribute_value AS av WHERE av.id_attr = p.id_attr  ) AS num_value
						FROM mc_attribute AS p
                            JOIN mc_attribute_content AS c ON(c.id_attr = p.id_attr)
							JOIN mc_lang AS lang ON ( c.id_lang = lang.id_lang )
							WHERE c.id_lang = :default_lang
						ORDER BY p.id_attr" . $limit;

                    if (isset($config['search'])) {
                        $cond = '';
                        if (is_array($config['search']) && !empty($config['search'])) {
                            $nbc = 0;
                            foreach ($config['search'] as $key => $q) {
                                if ($q !== '') {
                                    $cond .= 'AND ';
                                    //$p = 'p' . $nbc;
                                    //$cond .= !$nbc ? ' WHERE ' : 'AND ';
                                    switch ($key) {
                                        case 'id_attr':
                                            $cond .= ' p.' . $key . ' = ' . $q . ' ';
                                            break;
                                        case 'type_attr':
                                            $cond .= ' c.' . $key . ' = ' . '"'.$q .'"'. ' ';
                                            break;
                                        case 'date_register':
                                            $dateFormat = new component_format_date();
                                            $q = $dateFormat->date_to_db_format($q);
                                            $cond .= " p." . $key . " LIKE CONCAT('%', " . $q . ", '%') ";
                                            break;
                                    }
                                    //$params[$p] = $q;
                                    $nbc++;
                                }
                            }

                            $query = "SELECT p.id_attr, c.type_attr ,p.date_register, (SELECT count(id_attr_va) FROM mc_attribute_value AS av WHERE av.id_attr = p.id_attr  ) AS num_value
						FROM mc_attribute AS p
                            JOIN mc_attribute_content AS c ON(c.id_attr = p.id_attr)
							JOIN mc_lang AS lang ON ( c.id_lang = lang.id_lang )
							WHERE c.id_lang = :default_lang
                            $cond
                            ORDER BY p.id_attr" . $limit;
                        }
                    }
                    break;
                case 'page':
                    $query = 'SELECT p.*,c.*,lang.*
							FROM mc_attribute AS p
							JOIN mc_attribute_content AS c ON(c.id_attr = p.id_attr)
							JOIN mc_lang AS lang ON(c.id_lang = lang.id_lang)
							WHERE p.id_attr = :edit';
                    break;
                case 'attrvalue':
                    $query = 'SELECT v.id_content, p.id_attr_va, v.value_attr,v.id_lang,lang.iso_lang,lang.default_lang
							FROM mc_attribute_value AS p
							JOIN mc_attribute AS c ON(c.id_attr = p.id_attr)
                            JOIN mc_attribute_value_content AS v ON(v.id_attr_va = p.id_attr_va)
							JOIN mc_lang AS lang ON(v.id_lang = lang.id_lang)
							WHERE p.id_attr = :edit';
                    break;
                /*case 'cats':
                    $query = 'SELECT 
								c.id_cat,
								cc.name_cat
							FROM mc_catalog_cat AS c
							JOIN mc_catalog_cat_content AS cc ON(c.id_cat = cc.id_cat)
							JOIN mc_lang AS lang ON(cc.id_lang = lang.id_lang)
							WHERE cc.id_lang = :default_lang';
                    break;*/
                case 'attrCats':
                    $query = 'SELECT 
                                cat.id_attr_ca,
								c.id_cat,
								cc.name_cat
                            FROM mc_attribute_category as cat
							JOIN mc_catalog_cat AS c ON(cat.id_cat = c.id_cat)
							JOIN mc_catalog_cat_content AS cc ON(c.id_cat = cc.id_cat)
							JOIN mc_lang AS lang ON(cc.id_lang = lang.id_lang)
							WHERE cc.id_lang = :default_lang AND cat.id_attr = :id';
                    break;
                case 'langAttrByCat':
                    $query = 'SELECT 
                                cat.id_attr_ca,
								cat.id_attr,
								c.type_attr,
                                lang.iso_lang
                            FROM mc_attribute_category as cat
							JOIN mc_attribute AS p ON(cat.id_attr = p.id_attr)
							JOIN mc_attribute_content AS c ON(c.id_attr = p.id_attr)
							JOIN mc_lang AS lang ON(c.id_lang = lang.id_lang)
							WHERE lang.iso_lang = :iso AND cat.id_cat = :id';
                    break;
                case 'langValueByAttr':
                    $query = 'SELECT 
                                c.id_attr,
                                p.id_attr_va,
								v.value_attr,
                                lang.iso_lang
                            FROM mc_attribute_value AS p
							JOIN mc_attribute AS c ON(c.id_attr = p.id_attr)
                            JOIN mc_attribute_value_content AS v ON(v.id_attr_va = p.id_attr_va)
							JOIN mc_lang AS lang ON(v.id_lang = lang.id_lang)
							WHERE lang.iso_lang = :iso AND c.id_attr = :id';
                    break;
                case 'langValueByCat':
                    $query = 'SELECT 
                                c.id_attr,
                                v.id_attr_va,
								vc.value_attr,
                                lang.iso_lang
                            FROM mc_attribute_category as cat 
                            JOIN mc_attribute AS c ON(c.id_attr = cat.id_attr)
                            JOIN mc_attribute_value AS v ON(c.id_attr = v.id_attr)
                            JOIN mc_attribute_value_content AS vc ON(vc.id_attr_va = v.id_attr_va)
							JOIN mc_lang AS lang ON(vc.id_lang = lang.id_lang)
							WHERE lang.iso_lang = :iso AND c.id_attr = :id';
                    break;
                case 'langAttr':
                    $query = 'SELECT 
                                cat.id_attr_ca,
								cat.id_attr,
								c.type_attr,
                                lang.iso_lang
                            FROM mc_attribute_category as cat
							JOIN mc_attribute AS p ON(cat.id_attr = p.id_attr)
							JOIN mc_attribute_content AS c ON(c.id_attr = p.id_attr)
							JOIN mc_lang AS lang ON(c.id_lang = lang.id_lang)
							WHERE lang.iso_lang = :iso';
                    break;
                case 'langValueByProduct':
                    $query = 'SELECT 
                                ac.id_attr,
                                ap.id_attr_p,
                                ap.id_attr_va,
                                ap.price_p,
								vc.value_attr,
                                c.type_attr,
                                lang.iso_lang
                            FROM mc_attribute_product AS ap 
                            JOIN mc_attribute_value AS v ON(ap.id_attr_va = v.id_attr_va)
                            JOIN mc_attribute_value_content AS vc ON(vc.id_attr_va = v.id_attr_va)
                                JOIN mc_attribute AS ac ON(ac.id_attr = v.id_attr)
                                JOIN mc_attribute_content AS c ON(c.id_attr = ac.id_attr)
							JOIN mc_lang AS lang ON(vc.id_lang = lang.id_lang AND c.id_lang = lang.id_lang)
							WHERE lang.iso_lang = :iso AND ap.id_product = :id
                            ORDER BY order_attr_p';
                    break;
                case 'langValueInProduct':
                    $query = 'SELECT 
                                ac.id_attr,
                                ap.id_attr_p,
                                ap.id_attr_va,
                                ap.id_product,
                                ap.price_p,
								vc.value_attr,
                                c.type_attr,
                                lang.iso_lang
                            FROM mc_attribute_product AS ap 
                            JOIN mc_attribute_value AS v ON(ap.id_attr_va = v.id_attr_va)
                            JOIN mc_attribute_value_content AS vc ON(vc.id_attr_va = v.id_attr_va)
                                JOIN mc_attribute AS ac ON(ac.id_attr = v.id_attr)
                                JOIN mc_attribute_content AS c ON(c.id_attr = ac.id_attr)
							JOIN mc_lang AS lang ON(vc.id_lang = lang.id_lang AND c.id_lang = lang.id_lang)
							WHERE lang.iso_lang = :iso AND ap.id_product IN ('.$params['id'].')
                            ORDER BY id_attr, id_attr_va';
                    //$params = array();
                unset($params['id']);
                    break;
                case 'setPagesTree':
                    $query = "SELECT p.id_attr_va,v.value_attr ,c.id_attr AS id_parent, ac.type_attr AS name_parent
							FROM mc_attribute_value AS p
							JOIN mc_attribute AS c ON(c.id_attr = p.id_attr)
                            JOIN mc_attribute_content AS ac ON(ac.id_attr = c.id_attr)
                            JOIN mc_attribute_value_content AS v ON(v.id_attr_va = p.id_attr_va)
							JOIN mc_lang AS lang ON(v.id_lang = lang.id_lang)
							WHERE v.id_lang = :default_lang
							GROUP BY p.id_attr_va 
						ORDER BY name_parent ASC";
                    break;
                case 'cartpay_product':
                    $query = 'SELECT item.id_items,item.quantity,
                                IFNULL(map.price_p,p.price_p) AS price_p,
                                mcpc.name_p,vc.value_attr
                            FROM mc_cartpay_items AS item
                            JOIN mc_catalog_product AS p ON(item.id_product = p.id_product)
                            JOIN mc_catalog_product_content AS mcpc ON ( p.id_product = mcpc.id_product )
                            LEFT JOIN mc_cartpay_attribute mca on (item.id_items = mca.id_items)
                            LEFT JOIN mc_attribute_product map ON(p.id_product = map.id_product)
                            LEFT JOIN mc_attribute_value AS v ON(mca.id_attr_va = v.id_attr_va)
                            LEFT JOIN mc_attribute_value_content AS vc ON(vc.id_attr_va = v.id_attr_va)
                                LEFT JOIN mc_attribute AS ac ON(ac.id_attr = v.id_attr)
                                LEFT JOIN mc_attribute_content AS c ON(c.id_attr = ac.id_attr)
							LEFT JOIN mc_lang AS lang ON(mcpc.id_lang = lang.id_lang AND vc.id_lang = lang.id_lang AND c.id_lang = lang.id_lang)
                            WHERE mcpc.id_lang = :default_lang AND item.id_cart = :id
                            GROUP BY item.id_items';
                    break;
                case 'pagesPublishedSelect':
                    $query = "SELECT p.id_parent,p.id_cat, c.name_cat , ca.name_cat AS parent_cat
							FROM mc_catalog_cat AS p
								JOIN mc_catalog_cat_content AS c USING ( id_cat )
								JOIN mc_lang AS lang ON ( c.id_lang = lang.id_lang )
								LEFT JOIN mc_catalog_cat AS pa ON ( p.id_parent = pa.id_cat )
								LEFT JOIN mc_catalog_cat_content AS ca ON ( pa.id_cat = ca.id_cat ) 
								WHERE c.id_lang = :default_lang
								AND c.published_cat = 1
								GROUP BY p.id_cat 
							ORDER BY p.id_cat DESC";
                    break;
                default:
                    return false;
            }

            try {
                return component_routing_db::layer()->fetchAll($query, $params);
            }
            catch (Exception $e) {
                if(!isset($this->logger)) $this->logger = new debug_logger(MP_LOG_DIR);
                $this->logger->log('statement','db',$e->getMessage(),$this->logger::LOG_MONTH);
            }

        } elseif ($config['context'] === 'one') {
            switch ($config['type']) {
                case 'root':
                    $query = 'SELECT * FROM mc_attribute ORDER BY id_attr DESC LIMIT 0,1';
                    break;
                case 'page':
                    $query = 'SELECT * FROM mc_attribute WHERE `id_attr` = :edit';
                    break;
                case 'contentPage':
                    $query = 'SELECT * FROM `mc_attribute_content` 
                        WHERE `id_attr` = :id_attr AND `id_lang` = :id_lang';
                    break;
                case 'contentValue':
                    $query = 'SELECT * FROM `mc_attribute_value_content` 
                        WHERE `id_attr_va` = :id_attr_va AND `id_lang` = :id_lang';
                    break;
                case 'lastValue':
                    $query = 'SELECT p.*,c.*
							FROM mc_attribute_value AS p
							JOIN mc_attribute AS c USING(id_attr)
							WHERE p.id_attr = :id 
                            ORDER BY p.id_attr_va DESC LIMIT 0,1';
                    break;
                case 'lastLangValue':
                    $query = 'SELECT v.id_content, p.id_attr_va, v.value_attr,v.id_lang,lang.iso_lang,lang.default_lang
							FROM mc_attribute_value AS p
							JOIN mc_attribute AS c USING(id_attr)
                            JOIN mc_attribute_value_content AS v USING(id_attr_va)
                            JOIN mc_lang AS lang ON(v.id_lang = lang.id_lang)
							WHERE p.id_attr = :id AND v.id_lang = :default_lang
                            ORDER BY p.id_attr_va DESC LIMIT 0,1';
                    break;
                case 'lastCat':
                    $query = 'SELECT 
                                cat.id_attr_ca,
								c.id_cat,
								cc.name_cat
                            FROM mc_attribute_category as cat
							JOIN mc_catalog_cat AS c USING(id_cat)
							JOIN mc_catalog_cat_content AS cc USING(id_cat)
							JOIN mc_lang AS lang ON(cc.id_lang = lang.id_lang)
							WHERE cc.id_lang = :default_lang 
                            ORDER BY id_attr_ca DESC LIMIT 0,1';
                    break;
                case 'lastProduct':
                    $query = 'SELECT 
                                ap.id_attr_p,
                                ap.id_attr_va,
                                ap.id_product,
                                ap.price_p,
								vc.value_attr,
                                c.type_attr,
                                lang.iso_lang
                            FROM mc_attribute_product AS ap 
                            JOIN mc_attribute_value AS v ON(ap.id_attr_va = v.id_attr_va)
                            JOIN mc_attribute_value_content AS vc ON(vc.id_attr_va = v.id_attr_va)
                                JOIN mc_attribute AS ac ON(ac.id_attr = v.id_attr)
                                JOIN mc_attribute_content AS c ON(c.id_attr = ac.id_attr)
							JOIN mc_lang AS lang ON(vc.id_lang = lang.id_lang AND c.id_lang = lang.id_lang)
							WHERE vc.id_lang = :default_lang 
                            ORDER BY id_attr_p DESC LIMIT 0,1';
                    break;
                case 'priceByProduct':
                    $query = 'SELECT 
                                ap.id_attr_p,
                                ap.id_attr_va,
                                ap.price_p
								
                            FROM mc_attribute_product AS ap 
                            WHERE ap.id_product = :id_product AND ap.id_attr_va = :id';
                    break;
                case 'product_price':
                    $query = 'SELECT price_p FROM `mc_catalog_product` WHERE id_product = :id';
                    break;
                case 'paramValue':
                    $query = 'SELECT 
                                v.id_attr_va,
								vc.value_attr,
                                c.type_attr,
                                lang.iso_lang
                            FROM mc_attribute_value AS v 
                            JOIN mc_attribute_value_content AS vc ON(vc.id_attr_va = v.id_attr_va)
                                JOIN mc_attribute AS ac ON(ac.id_attr = v.id_attr)
                                JOIN mc_attribute_content AS c ON(c.id_attr = ac.id_attr)
							JOIN mc_lang AS lang ON(vc.id_lang = lang.id_lang AND c.id_lang = lang.id_lang)
							WHERE lang.iso_lang = :iso AND v.id_attr_va = :id';
                    break;
                case 'cartpay':
                    $query = 'SELECT * 
                    FROM `mc_cartpay_attribute` WHERE id_items = :id AND id_attr_va = :id_attr_va';
                    break;
                default:
                    return false;
            }

            try {
                return component_routing_db::layer()->fetch($query, $params);
            }
            catch (Exception $e) {
                if(!isset($this->logger)) $this->logger = new debug_logger(MP_LOG_DIR);
                $this->logger->log('statement','db',$e->getMessage(),$this->logger::LOG_MONTH);
            }
        }
        return false;
    }
    /**
     * @param array $config
     * @param array $params
     * @return bool|string
     */
    public function insert(array $config, array $params = []) {
        switch ($config['type']) {
            case 'page':
                $query = "INSERT INTO mc_attribute (date_register)
                        VALUE (NOW())";
                break;
            case 'contentPage':
                $query = 'INSERT INTO `mc_attribute_content`(id_attr,id_lang,type_attr) 
				  		VALUES (:id_attr,:id_lang,:type_attr)';
                break;
            case 'value':
                $query = "INSERT INTO mc_attribute_value (id_attr, date_register)
                        VALUE (:id_attr, NOW())";
                break;
            case 'contentValue':
                $query = "INSERT INTO mc_attribute_value_content (id_attr_va, id_lang, value_attr)
                        VALUE (:id_attr_va, :id_lang, :value_attr)";
                break;
            case 'cat':
                $query = "INSERT INTO mc_attribute_category (id_attr, id_cat, date_register)
                        VALUE (:id_attr, :id_cat, NOW())";
                break;
            /*case 'product':
                $query = "INSERT INTO mc_attribute_product (id_attr_va, id_product, price_p, date_register)
                        VALUE (:id_attr_va, :id_product, :price_p, NOW())";
                break;*/
            case 'cartpay':
                $query = "INSERT INTO mc_cartpay_attribute (id_attr_va, id_items, date_register)
                        VALUE (:id_attr_va, :id_items, NOW())";
                break;
            case 'product':
                $query = "INSERT INTO mc_attribute_product (id_attr_va, id_product, price_p,date_register, order_attr_p)
                        SELECT :id_attr_va, :id_product, :price_p, NOW(), COUNT(id_attr_va) FROM mc_attribute_product WHERE id_product = '".$params['id_product']."'";
                break;
            default:
                return false;
        }

        try {
            component_routing_db::layer()->insert($query,$params);
            return true;
        }
        catch (Exception $e) {
            return 'Exception reçue : '.$e->getMessage();
        }
    }
    /**
     * @param array $config
     * @param array $params
     * @return bool|string
     */
    public function update(array $config, array $params = []) {
        switch ($config['type']) {
            case 'contentPage':
                $query = 'UPDATE mc_attribute_content 
						SET 
							type_attr = :type_attr
                		WHERE id_attr = :id_attr AND id_lang = :id_lang';
                break;
            case 'contentValue':
                $query = 'UPDATE mc_attribute_value_content 
						SET 
							value_attr = :value_attr
                		WHERE id_attr_va = :id_attr_va AND id_lang = :id_lang';
                break;
            case 'order':
                $query = 'UPDATE mc_attribute_product 
						SET order_attr_p = :order_attr_p
						WHERE id_attr_p = :id_attr_p';
                break;
            default:
                return false;
        }

        try {
            component_routing_db::layer()->update($query,$params);
            return true;
        }
        catch (Exception $e) {
            return 'Exception reçue : '.$e->getMessage();
        }
    }
    /**
     * @param $config
     * @param array $params
     * @return bool|string
     */
    public function delete($config, $params = array())
    {
        if (!is_array($config)) return '$config must be an array';
        $query = '';

        switch ($config['type']) {
            case 'delPages':
                $query = 'DELETE FROM mc_attribute 
						WHERE id_attr IN ('.$params['id'].')';
                $params = array();
                break;
            case 'delValue':
                $query = 'DELETE FROM mc_attribute_value 
						WHERE id_attr_va IN ('.$params['id'].')';
                $params = array();
                break;
            case 'delCat':
                $query = 'DELETE FROM mc_attribute_category
						WHERE id_attr_ca IN ('.$params['id'].')';
                $params = array();
                break;
            case 'delProduct':
                $query = 'DELETE FROM mc_attribute_product
						WHERE id_attr_p IN ('.$params['id'].')';
                $params = array();
                break;
        }

        if($query === '') return 'Unknown request asked';

        try {
            component_routing_db::layer()->delete($query,$params);
            return true;
        }
        catch (Exception $e) {
            return 'Exception reçue : '.$e->getMessage();
        }
    }
}