{if isset($attributes) && is_Array($attributes)}
    {foreach $attributes as $type => $options}
        <div class="form-group">
            <label for="param[attribute][{$type}]">{#ph_transport_city#|ucfirst}</label>
            <select name="param[attribute][{$type}]" id="param[attribute][{$type}]" class="form-control required" required>
                {*<option disabled selected>-- {#pn_transport_city#|ucfirst} --</option>*}
                {foreach $options as $value}
                    <option value="{$value.id}">{$value.type} : {$value.name}</option>
                {/foreach}
            </select>
        </div>
    {/foreach}
{/if}