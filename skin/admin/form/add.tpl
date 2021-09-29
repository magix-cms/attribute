{include file="language/brick/dropdown-lang.tpl"}
<form id="add_attribute" action="{$smarty.server.SCRIPT_NAME}?controller={$smarty.get.controller}&amp;action=add" method="post" class="validate_form add_form">
    <div class="tab-content">
    {foreach $langs as $id => $iso}
    <fieldset role="tabpanel" class="tab-pane{if $iso@first} active{/if}" id="lang-{$id}">
        <div class="row">
            <div class="col-ph-12 col-md-6 col-lg-6">
                <div class="form-group">
                    <label for="attrData[{$id}][type_attr]">{#type_attr#|ucfirst} :</label>
                    <input type="text" class="form-control" id="attrData[{$id}][type_attr]" name="attrData[{$id}][type_attr]" value="{$page.content[{$id}].type_attr}" size="50" />
                </div>
            </div>
        </div>
    </fieldset>
    {/foreach}
    </div>
    <div id="submit">
        <button class="btn btn-main-theme" type="submit" name="action" value="add">{#save#|ucfirst}</button>
    </div>
</form>