{strip}
    {if isset($row)}
        {$attrvalue = $row}
    {/if}
    {capture name="content"}
        <td>{$attrvalue.id}</td>
        <td>{$attrvalue.type}</td>
        <td>{$attrvalue.name}</td>
        <td>{if $attrvalue.price}{$attrvalue.price|string_format:"%.2f"}&nbsp;&euro;{elseif $attrvalue.price == null}&mdash;{else}{#price_0#|ucfirst}{/if}</td>
    {/capture}
{/strip}
{include file="loop/product-rows.tpl" controller="attribute" sub="attribute" content=$smarty.capture.content idc=$id id=$attrvalue.id editableRow=false}