{vat_data id_product=$product.id}
{if isset($attributes) && is_Array($attributes)}
    {foreach $attributes as $type => $options}
        <div class="form-group">
            {*<label for="param[attribute][{$type}]">{#ph_transport_city#|ucfirst}</label>*}
            <select name="param[attribute][{$type}]" id="param[attribute][{$type}]" data-price-replacer="true" class="form-control required" required>
                {*<option disabled selected>-- {#pn_transport_city#|ucfirst} --</option>*}
                {foreach $options as $value}
                    {*{$price_attr = $value.price * (1 + ($vat/100))}*}
                    <option value="{$value.id}"{if $value.price} data-price="{$value.price}" data-vat="{$vat}"{/if}>{$value.type} : {$value.name}</option>
                {/foreach}
            </select>
        </div>
    {/foreach}
{/if}