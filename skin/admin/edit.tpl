{extends file="layout.tpl"}
{block name='head:title'}{#edit_attribute#|ucfirst}{/block}
{block name='body:id'}attribute{/block}

{block name='article:header'}
    <h1 class="h2"><a href="{$smarty.server.SCRIPT_NAME}?controller={$smarty.get.controller}" title="Afficher la liste des attribute">{#attribute#|ucfirst}</a></h1>
{/block}
{block name='article:content'}
    {if {employee_access type="append" class_name=$cClass} eq 1}
        <div class="panels row">
            <section class="panel col-xs-12 col-md-12">
                {if $debug}
                    {$debug}
                {/if}
                <header class="panel-header panel-nav">
                    <h2 class="panel-heading h5">{#edit_attribute#|ucfirst}</h2>
                    <ul class="nav nav-tabs" role="tablist">
                        <li role="presentation" class="active"><a href="#general" aria-controls="general" role="tab" data-toggle="tab">{#group#}</a></li>
                        <li role="presentation"><a href="#value" aria-controls="value" role="tab" data-toggle="tab">{#value#}</a></li>
                        <li role="presentation"><a href="#category" aria-controls="category" role="tab" data-toggle="tab">{#category#}</a></li>
                    </ul>
                </header>
                <div class="panel-body panel-body-form">
                    <div class="mc-message-container clearfix">
                        <div class="mc-message"></div>
                    </div>
                    <div class="tab-content">
                        <div role="tabpanel" class="tab-pane active" id="general">
                            {include file="form/edit.tpl"}
                        </div>
                        <div role="tabpanel" class="tab-pane" id="value">
                            {include file="section/form/list-form.tpl" controller="attribute" sub="value" controller_extend=false dir_controller="" data=$attrvalue id=$attrvalue.id_attr_va class_form="col-ph-12 col-lg-4" class_table="col-ph-12 col-lg-8"}
                        </div>
                        <div role="tabpanel" class="tab-pane" id="category">
                            {include file="section/form/list-form.tpl" controller="attribute" sub="category" controller_extend=false dir_controller="" data=$attrCats id=$attrCats.id_attr_ca class_form="col-ph-12 col-lg-4" class_table="col-ph-12 col-lg-8"}
                        </div>
                    </div>
                </div>
            </section>
        </div>
        {include file="modal/delete.tpl" data_type='attribute' title={#modal_delete_title#|ucfirst} info_text=true delete_message={#delete_pages_message#}}
    {/if}
{/block}
{block name="foot" append}
    {capture name="scriptForm"}{strip}
        /{baseadmin}/min/?f=libjs/vendor/jquery-ui-1.12.min.js,
        libjs/vendor/tabcomplete.min.js,
        libjs/vendor/livefilter.min.js,
        libjs/vendor/bootstrap-select.min.js,
        libjs/vendor/filterlist.min.js
    {/strip}{/capture}
    {script src=$smarty.capture.scriptForm type="javascript"}
{/block}