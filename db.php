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
                            JOIN mc_attribute_content AS c USING ( id_attr )
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
                            JOIN mc_attribute_content AS c USING ( id_attr )
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
							JOIN mc_attribute_content AS c USING(id_attr)
							JOIN mc_lang AS lang ON(c.id_lang = lang.id_lang)
							WHERE p.id_attr = :edit';
                    break;
                case 'attrvalue':
                    $sql = 'SELECT v.id_content, p.id_attr_va, v.value_attr,v.id_lang,lang.iso_lang,lang.default_lang
							FROM mc_attribute_value AS p
							JOIN mc_attribute AS c USING(id_attr)
                            JOIN mc_attribute_value_content AS v USING(id_attr_va)
							JOIN mc_lang AS lang ON(v.id_lang = lang.id_lang)
							WHERE p.id_attr = :edit
							ORDER BY p.id_attr_va DESC';
                    break;
                case 'cats':
                    $sql = 'SELECT 
								c.id_cat,
								cc.name_cat
							FROM mc_catalog_cat AS c
							JOIN mc_catalog_cat_content AS cc USING(id_cat)
							JOIN mc_lang AS lang ON(cc.id_lang = lang.id_lang)
							WHERE cc.id_lang = :default_lang';
                    break;
                case 'attrCats':
                    $sql = 'SELECT 
                                cat.id_attr_ca,
								c.id_cat,
								cc.name_cat
                            FROM mc_attribute_category as cat
							JOIN mc_catalog_cat AS c USING(id_cat)
							JOIN mc_catalog_cat_content AS cc USING(id_cat)
							JOIN mc_lang AS lang ON(cc.id_lang = lang.id_lang)
							WHERE cc.id_lang = :default_lang';
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
        }

        if($sql === '') return 'Unknown request asked';

        try {
            component_routing_db::layer()->insert($sql,$params);
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
        }

        if($sql === '') return 'Unknown request asked';

        try {
            component_routing_db::layer()->update($sql,$params);
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
        }

        if($sql === '') return 'Unknown request asked';

        try {
            component_routing_db::layer()->delete($sql,$params);
            return true;
        }
        catch (Exception $e) {
            return 'Exception reçue : '.$e->getMessage();
        }
    }
}