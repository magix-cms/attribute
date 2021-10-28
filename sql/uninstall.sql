TRUNCATE TABLE `mc_attribute_product`;
DROP TABLE `mc_attribute_product`;
TRUNCATE TABLE `mc_attribute_category`;
DROP TABLE `mc_attribute_category`;
TRUNCATE TABLE `mc_attribute_value_content`;
DROP TABLE `mc_attribute_value_content`;
TRUNCATE TABLE `mc_attribute_value`;
DROP TABLE `mc_attribute_value`;
TRUNCATE TABLE `mc_attribute_content`;
DROP TABLE `mc_attribute_content`;
TRUNCATE TABLE `mc_attribute`;
DROP TABLE `mc_attribute`;

DELETE FROM `mc_plugins_module` WHERE `module_name` = 'attribute';

DELETE FROM `mc_plugins` WHERE `name` = 'attribute';

DELETE FROM `mc_admin_access` WHERE `id_module` IN (
    SELECT `id_module` FROM `mc_module` as m WHERE m.name = 'attribute'
);