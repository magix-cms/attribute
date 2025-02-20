{if isset($product.attributes) && is_array($product.attributes)}
    {*<pre>{$attributes|print_r}</pre>*}
    {foreach $attributes as $options}
        {$type = $options[0]}
        {*<div class="form-group">
            <select name="param[attribute][{$type.id_type}]" id="attribute_{$type.id_type}" data-price-replacer="true" class="form-control required" required>
                {foreach $options as $value}
                    <option value="{$value.id}"{if $value.price} data-price="{$value.price}" data-vat="{if isset($vat)}{$vat}{else}{if $setting.price_display === 'tinc'}{$item.total_inc|string_format:"%.2f"}{else}{$item.total|string_format:"%.2f"}{/if}{/if}"{/if}>{$value.name}</option>
                {/foreach}
            </select>
            <label for="attribute_{$type.id_type}">{$type.type}</label>
        </div>*}
        {*<div class="form-group">
            <select name="param[attribute][{$type.id_type}]" id="attribute_{$type.id_type}" data-price-replacer="true" class="form-control required" required>
                {foreach $options as $value}
                    <option value="{$value.id}"{if $value.price} data-price="{$value.price}" data-vat="{if isset($vat)}{$vat}{else}{if $setting.price_display === 'tinc'}{$item.total_inc|string_format:"%.2f"}{else}{$item.total|string_format:"%.2f"}{/if}{/if}"{/if}>{$value.name}</option>
                {/foreach}
            </select>
            <label for="attribute_{$type.id_type}">{$type.type}</label>
        </div>*}
        <div class="row">
            <div class="col-12">
                <span class="h4">{$type.type}</span>
            </div>
            <div class="col-12">
                <div class="radio-group">
                    {foreach $options as $value}
                        <input type="radio" name="param[attribute][{$type.id_type}]" id="attribute_{$type.id_type}_{$value.id}" data-price-replacer="true" value="{$value.id}"{if $value.price} data-price="{$value.price}" data-vat="{if isset($vat)}{$vat}{else}{if $setting.price_display === 'tinc'}{$item.total_inc|string_format:"%.2f"}{else}{$item.total|string_format:"%.2f"}{/if}{/if}"{/if} required>
                        <label for="attribute_{$type.id_type}_{$value.id}">{$value.name}</label>
                    {/foreach}
                </div>
            </div>
        </div>
    {/foreach}
{/if}