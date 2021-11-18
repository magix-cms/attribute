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
#### Avec widget
```smarty
{attribute_data id=$product.id type="product"}
{if is_array($attribute) && !empty($attribute)}
{foreach $attribute as $item}
<p>{$item.type} : {$item.name}</p>
{/foreach}
{/if}
````
#### sans widget (override)
Product
```smarty
{if is_array($product.attributes) && !empty($product.attributes)}
{foreach $product.attributes as $item}
<p>{$item.type} : {$item.name}</p>
{/foreach}
{/if}
````
Product loop
```smarty
{if is_array($item.attributes) && !empty($item.attributes)}
{foreach $item.attributes as $key}
<span>{$key.type} : {$key.name}</span>
{/foreach}
{/if}
````
Ad To Cart
```smarty
{if is_array($product.attributes) && !empty($product.attributes)}
<div class="form-group">
<label for="param[attribute]">{#ph_transport_city#|ucfirst}</label>
<select name="param[attribute]" id="param[attribute]" class="form-control required" required>
{*<option disabled selected>-- {#pn_transport_city#|ucfirst} --</option>*}
{foreach $product.attributes as $item}
<option value="{$item.id}">{$item.type} : {$item.name}</option>
{/foreach}
</select>
</div>
{/if}
````