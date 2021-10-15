{strip}
    {if isset($row)}
        {$attrvalue = $row}
    {/if}
    {capture name="content"}
        <td>{$attrvalue.id}</td>
        <td>{$attrvalue.type}</td>
        <td>{$attrvalue.name}</td>
    {/capture}
{/strip}
{include file="loop/product-rows.tpl" controller="attribute" sub="attribute" content=$smarty.capture.content idc=$id id=$attrvalue.id editableRow=false}