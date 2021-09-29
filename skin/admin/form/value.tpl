<div class="form-group">
    <div class="dropdown dropdown-lang">
        <button id="dp-lang-{$id}" class="btn btn-default dropdown-toggle{if $custom_class} {$custom_class}{/if}" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
            {foreach $langs as $id_lang => $iso}
                {if $iso@first}{$default = $id_lang}{break}{/if}
            {/foreach}
            <span class="lang">{$langs[$default]}</span>
            <span class="caret"></span>
        </button>
        <ul class="dropdown-menu" aria-describedby="dp-lang-{$id}" role="tablist">
            {foreach $langs as $id_lang => $iso}
                <li role="presentation"{if $iso@first} class="active"{/if}>
                    <a data-target="#l{$id}-lang-{$id_lang}" aria-controls="lang-{$id_lang}" role="tab" data-toggle="tab">{$iso}</a>
                </li>
            {/foreach}
        </ul>
    </div>
</div>
{*<div class="form-group">
    <label for="value_attr">{#value_attr#}&nbsp;:</label>
    <input type="text" name="attrData[value_attr]" id="value_attr" class="form-control" value="" />
</div>
<div id="submit">
    <input type="hidden" name="id" value="{$page.id_attr}" />
    <button class="btn btn-main-theme pull-right" type="submit" name="action" value="edit">{#add#|ucfirst}</button>
</div>*}
<div class="tab-content">
{foreach $langs as $id_lang => $iso}
    <fieldset role="tabpanel" class="tab-pane{if $iso@first} active{/if}" id="l{$id}-lang-{$id_lang}">
        <div class="form-group">
            <label for="attrValue[{$id_lang}][value_attr]">{#value_attr#|ucfirst} :</label>
            <input type="text" class="form-control" id="attrValue[{$id_lang}][value_attr]" name="attrValue[{$id_lang}][value_attr]" value="{$attrvalue.content[{$id_lang}].value_attr}" size="50" />
        </div>
    </fieldset>
{/foreach}
</div>
{if $editableRow}
<input type="hidden" name="id_attr_va" value="{$id}" />
<button class="btn btn-main-theme" type="submit">Enregistrer</button>
<button class="btn btn-link text-success hide" type="button"><span class="fa fa-check"></span>&nbsp;{#saved#|ucfirst}</button>
{else}
    <div id="submit">
        <input type="hidden" id="id_attr" name="id" value="{$page.id_attr}">
        <button class="btn btn-main-theme" type="submit" name="action" value="edit">{#save#|ucfirst}</button>
    </div>
{/if}