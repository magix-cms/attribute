{strip}
    {if isset($row)}
        {$attrCats = $row}
    {/if}
    {capture name="content"}
        <td>{$attrCats.id_attr_ca}</td>
        <td>{$attrCats.name_cat}</td>
    {/capture}
{/strip}
{include file="loop/cat-rows.tpl" controller="attribute" sub="category" content=$smarty.capture.content idc=$id id=$attrCats.id_attr_ca editableRow=false}