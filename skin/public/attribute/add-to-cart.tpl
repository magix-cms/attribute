{if isset($product.attributes) && is_array($product.attributes)}
    {foreach $attributes as $type => $options}
        <div class="form-group">
            <select name="param[attribute][{$type.id_type}]" id="param[attribute][{$type.id_type}]" data-price-replacer="true" class="form-control required" required>
                {foreach $options as $value}
                    <option value="{$value.id}"{if $value.price} data-price="{$value.price}" data-vat="{if isset($vat)}{$vat}{else}{if $setting.price_display === 'tinc'}{$item.total_inc|string_format:"%.2f"}{else}{$item.total|string_format:"%.2f"}{/if}{/if}"{/if}>{$value.type} : {$value.name}</option>
                {/foreach}
            </select>
        </div>
    {/foreach}
{/if}