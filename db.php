<?php
class plugins_attribute_db
{
    /**
     * @param $config
     * @param bool $params
     * @return mixed|null
     * @throws Exception
     */
    public function fetchData($config, $params = false)
    {
        if (!is_array($config)) return '$config must be an array';

        $sql = '';
        $dateFormat = new component_format_date();

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

                    $sql = "SELECT p.id_attr, c.type_attr ,p.date_register, (SELECT count(id_attr_va) FROM mc_attribute_value AS av WHERE av.id_attr = p.id_attr  ) AS num_value
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
                                            $q = $dateFormat->date_to_db_format($q);
                                            $cond .= " p." . $key . " LIKE CONCAT('%', " . $q . ", '%') ";
                                            break;
                                    }
                                    //$params[$p] = $q;
                                    $nbc++;
                                }
                            }

                            $sql = "SELECT p.id_attr, c.type_attr ,p.date_register, (SELECT count(id_attr_va) FROM mc_attribute_value AS av WHERE av.id_attr = p.id_attr  ) AS num_value
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
                    $sql = 'SELECT p.*,c.*,lang.*
							FROM mc_attribute AS p
							JOIN mc_attribute_content AS c ON(c.id_attr = p.id_attr)
							JOIN mc_lang AS lang ON(c.id_lang = lang.id_lang)
							WHERE p.id_attr = :edit';
                    break;
                case 'attrvalue':
                    $sql = 'SELECT v.id_content, p.id_attr_va, v.value_attr,v.id_lang,lang.iso_lang,lang.default_lang
							FROM mc_attribute_value AS p
							JOIN mc_attribute AS c ON(c.id_attr = p.id_attr)
                            JOIN mc_attribute_value_content AS v ON(v.id_attr_va = p.id_attr_va)
							JOIN mc_lang AS lang ON(v.id_lang = lang.id_lang)
							WHERE p.id_attr = :edit';
                    break;
                /*case 'cats':
                    $sql = 'SELECT 
								c.id_cat,
								cc.name_cat
							FROM mc_catalog_cat AS c
							JOIN mc_catalog_cat_content AS cc ON(c.id_cat = cc.id_cat)
							JOIN mc_lang AS lang ON(cc.id_lang = lang.id_lang)
							WHERE cc.id_lang = :default_lang';
                    break;*/
                case 'attrCats':
                    $sql = 'SELECT 
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
                    $sql = 'SELECT 
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
                    $sql = 'SELECT 
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
                    $sql = 'SELECT 
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
                    $sql = 'SELECT 
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
                    $sql = 'SELECT 
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
                    $sql = 'SELECT 
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
                    $sql = "SELECT p.id_attr_va,v.value_attr ,c.id_attr AS id_parent, ac.type_attr AS name_parent
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
                    $sql = 'SELECT item.id_items,item.quantity,
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
                    $sql = "SELECT p.id_parent,p.id_cat, c.name_cat , ca.name_cat AS parent_cat
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
            }

            return $sql ? component_routing_db::layer()->fetchAll($sql, $params) : null;
        } elseif ($config['context'] === 'one') {
            switch ($config['type']) {
                case 'root':
                    $sql = 'SELECT * FROM mc_attribute ORDER BY id_attr DESC LIMIT 0,1';
                    break;
                case 'page':
                    $sql = 'SELECT * FROM mc_attribute WHERE `id_attr` = :edit';
                    break;
                case 'contentPage':
                    $sql = 'SELECT * FROM `mc_attribute_content` 
                        WHERE `id_attr` = :id_attr AND `id_lang` = :id_lang';
                    break;
                case 'contentValue':
                    $sql = 'SELECT * FROM `mc_attribute_value_content` 
                        WHERE `id_attr_va` = :id_attr_va AND `id_lang` = :id_lang';
                    break;
                case 'lastValue':
                    $sql = 'SELECT p.*,c.*
							FROM mc_attribute_value AS p
							JOIN mc_attribute AS c USING(id_attr)
							WHERE p.id_attr = :id 
                            ORDER BY p.id_attr_va DESC LIMIT 0,1';
                    break;
                case 'lastLangValue':
                    $sql = 'SELECT v.id_content, p.id_attr_va, v.value_attr,v.id_lang,lang.iso_lang,lang.default_lang
							FROM mc_attribute_value AS p
							JOIN mc_attribute AS c USING(id_attr)
                            JOIN mc_attribute_value_content AS v USING(id_attr_va)
                            JOIN mc_lang AS lang ON(v.id_lang = lang.id_lang)
							WHERE p.id_attr = :id AND v.id_lang = :default_lang
                            ORDER BY p.id_attr_va DESC LIMIT 0,1';
                    break;
                case 'lastCat':
                    $sql = 'SELECT 
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
                    $sql = 'SELECT 
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
                    $sql = 'SELECT 
                                ap.id_attr_p,
                                ap.id_attr_va,
                                ap.price_p
								
                            FROM mc_attribute_product AS ap 
                            WHERE ap.id_product = :id_product AND ap.id_attr_va = :id';
                    break;
                case 'product_price':
                    $sql = 'SELECT price_p FROM `mc_catalog_product` WHERE id_product = :id';
                    break;
                case 'paramValue':
                    $sql = 'SELECT 
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
                    $sql = 'SELECT * 
                    FROM `mc_cartpay_attribute` WHERE id_items = :id AND id_attr_va = :id_attr_va';
                    break;
            }

            return $sql ? component_routing_db::layer()->fetch($sql, $params) : null;
        }
    }
    /**
     * @param $config
     * @param array $params
     * @return bool|string
     */
    public function insert($config,$params = array())
    {
        if (!is_array($config)) return '$config must be an array';

        $sql = '';

        switch ($config['type']) {
            case 'page':
                $sql = "INSERT INTO mc_attribute (date_register)
                        VALUE (NOW())";
                break;
            case 'contentPage':
                $sql = 'INSERT INTO `mc_attribute_content`(id_attr,id_lang,type_attr) 
				  		VALUES (:id_attr,:id_lang,:type_attr)';
                break;
            case 'value':
                $sql = "INSERT INTO mc_attribute_value (id_attr, date_register)
                        VALUE (:id_attr, NOW())";
                break;
            case 'contentValue':
                $sql = "INSERT INTO mc_attribute_value_content (id_attr_va, id_lang, value_attr)
                        VALUE (:id_attr_va, :id_lang, :value_attr)";
                break;
            case 'cat':
                $sql = "INSERT INTO mc_attribute_category (id_attr, id_cat, date_register)
                        VALUE (:id_attr, :id_cat, NOW())";
                break;
            /*case 'product':
                $sql = "INSERT INTO mc_attribute_product (id_attr_va, id_product, price_p, date_register)
                        VALUE (:id_attr_va, :id_product, :price_p, NOW())";
                break;*/
            case 'cartpay':
                $sql = "INSERT INTO mc_cartpay_attribute (id_attr_va, id_items, date_register)
                        VALUE (:id_attr_va, :id_items, NOW())";
                break;
            case 'product':
                $sql = "INSERT INTO mc_attribute_product (id_attr_va, id_product, price_p,date_register, order_attr_p)
                        SELECT :id_attr_va, :id_product, :price_p, NOW(), COUNT(id_attr_va) FROM mc_attribute_product WHERE id_product = '".$params['id_product']."'";
                break;
        }

        if($sql === '') return 'Unknown request asked';

        try {
            component_routing_db::layer()->insert($sql,$params);
            return true;
        }
        catch (Exception $e) {
            return 'Exception re??ue : '.$e->getMessage();
        }
    }
    /**
     * @param $config
     * @param array $params
     * @return bool|string
     */
    public function update($config,$params = array())
    {
        if (!is_array($config)) return '$config must be an array';

        $sql = '';

        switch ($config['type']) {
            case 'contentPage':
                $sql = 'UPDATE mc_attribute_content 
						SET 
							type_attr = :type_attr
                		WHERE id_attr = :id_attr AND id_lang = :id_lang';
                break;
            case 'contentValue':
                $sql = 'UPDATE mc_attribute_value_content 
						SET 
							value_attr = :value_attr
                		WHERE id_attr_va = :id_attr_va AND id_lang = :id_lang';
                break;
            case 'order':
                $sql = 'UPDATE mc_attribute_product 
						SET order_attr_p = :order_attr_p
						WHERE id_attr_p = :id_attr_p';
                break;
        }

        if($sql === '') return 'Unknown request asked';

        try {
            component_routing_db::layer()->update($sql,$params);
            return true;
        }
        catch (Exception $e) {
            return 'Exception re??ue : '.$e->getMessage();
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
        $sql = '';

        switch ($config['type']) {
            case 'delPages':
                $sql = 'DELETE FROM mc_attribute 
						WHERE id_attr IN ('.$params['id'].')';
                $params = array();
                break;
            case 'delValue':
                $sql = 'DELETE FROM mc_attribute_value 
						WHERE id_attr_va IN ('.$params['id'].')';
                $params = array();
                break;
            case 'delCat':
                $sql = 'DELETE FROM mc_attribute_category
						WHERE id_attr_ca IN ('.$params['id'].')';
                $params = array();
                break;
            case 'delProduct':
                $sql = 'DELETE FROM mc_attribute_product
						WHERE id_attr_p IN ('.$params['id'].')';
                $params = array();
                break;
        }

        if($sql === '') return 'Unknown request asked';

        try {
            component_routing_db::layer()->delete($sql,$params);
            return true;
        }
        catch (Exception $e) {
            return 'Exception re??ue : '.$e->getMessage();
        }
    }
}