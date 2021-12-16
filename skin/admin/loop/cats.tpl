{foreach $cats as $c}
<li class="filter-item items" data-filter="{$c.name_cat}" data-value="{$c.id_cat}" data-id="{$c.id_cat}">
    {$c.name_cat|ucfirst}{if $c.id_parent != '0'}<small>({$c.id_cat})</small>{/if}
    {if $c.subdata}
        <li class="optgroup">
            <ul class="list-unstyled">
                {include file="loop/cats.tpl" cats=$c.subdata}
            </ul>
        </li>
    {/if}
    </li>
{/foreach}