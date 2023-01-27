{strip}
    {if isset($row)}
        {$attrvalue = $row}
    {/if}
    {capture name="content"}
        <td>{$attrvalue.id_attr_va}</td>
        <td>{$attrvalue.value_attr}</td>
    {/capture}
{/strip}
{*<pre>{$attrvalue|print_r}</pre>*}
{include file="loop/list-rows.tpl" controller="attribute" sub="value" content=$smarty.capture.content idc=$id id=$attrvalue.id_attr_va editableRow=true}