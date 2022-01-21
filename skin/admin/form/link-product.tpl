{extends file="catalog/{$smarty.get.controller}/edit.tpl"}
{block name="plugin:content"}
    {*<pre>{$attrvalue|print_r}</pre>*}
    {include file="form/list-form.tpl" controller="attribute" sub="attribute" sortable=true controller_extend=true dir_controller="" data=$attrvalue id=$attrvalue.id class_form="col-ph-12 col-lg-6" class_table="col-ph-12 col-lg-6"}
    {include file="modal/delete.tpl" controller="attribute" data_type='attribute' title={#modal_delete_title#|ucfirst} info_text=true delete_message={#delete_pages_message#}}
{/block}
{block name="foot"}
    {capture name="scriptForm"}{strip}
        /{baseadmin}/min/?f=
        libjs/vendor/jquery-ui-1.12.min.js,
        libjs/vendor/tabcomplete.min.js,
        libjs/vendor/livefilter.min.js,
        libjs/vendor/src/bootstrap-select.js,
        libjs/vendor/filterlist.min.js,
        {baseadmin}/template/js/table-form.min.js,
        plugins/attribute/js/admin.min.js
    {/strip}{/capture}
    {script src=$smarty.capture.scriptForm type="javascript"}

    <script type="text/javascript">
        $(function(){
            var controller = "{$smarty.server.SCRIPT_NAME}?controller={$smarty.get.controller}";
            if (typeof tableForm == "undefined")
            {
                console.log("tableForm is not defined");
            }else{
                tableForm.run(controller);
            }
            if (typeof attribute == "undefined")
            {
                console.log("attribute is not defined");
            }else{
                attribute.run("{$smarty.server.SCRIPT_NAME}?controller=attribute");
            }
        });
    </script>
{/block}