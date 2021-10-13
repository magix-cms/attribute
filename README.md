# Attribute
Plugin attribute for Magix CMS 3

Ajouter des attributs dans votre site.

## Installation
* Décompresser l'archive dans le dossier "plugins" de magix cms
* Connectez-vous dans l'administration de votre site internet
* Cliquer sur l'onglet plugins du menu déroulant pour sélectionner attribute.
* Une fois dans le plugin, laisser faire l'auto installation
* Il ne reste que la configuration du plugin pour correspondre avec vos données.

### Infos
Ce plugin est utilisé pour compléter d'autres plugins comme "cartpay" ou tout autre système ayant besoin d'attributs.

### Exemple d'utilisation

```smarty
{attribute_data id=$product.id type="product"}
{if is_array($attribute) && !empty($attribute)}
{foreach $attribute as $item}
<p>{$item.type} : {$item.name}</p>
{/foreach}
{/if}
````
