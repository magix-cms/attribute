<fieldset>
<h2>{#add_attribute#}</h2>
    <div class="row">
        <div class="col-ph-12 col-md-4">
            <div class="form-group">
                <label for="attributes_id">{#id#|ucfirst} {#attributes#}&nbsp;</label>
                <input type="text" name="attributes_id" id="attributes_id" class="form-control mygroup required" placeholder="{#ph_id#}" value="{$page.id_cs}" required />
            </div>
        </div>
        <div class="col-ph-12 col-md-8">
            <div class="form-group">
                <label for="attributes">{#attributes#|ucfirst}&nbsp;</label>
                <div id="attributes" {*data-section="home"*} class="btn-group btn-block selectpicker" data-clear="true" data-live="true">
                    <a href="#" class="clear"><span class="fa fa-times"></span><span class="sr-only">Annuler la sélection</span></a>
                    <button data-id="attributes" type="button" class="btn btn-block btn-default dropdown-toggle">
                        <span class="placeholder">{#ph_attributes#|ucfirst}</span>
                        <span class="caret"></span>
                    </button>
                    <div class="dropdown-menu">
                        <div class="live-filtering" data-clear="true" data-autocomplete="true" data-keys="true">
                            <label class="sr-only" for="input-attributes">Rechercher dans la liste</label>
                            <div class="search-box">
                                <div class="input-group">
                                        <span class="input-group-addon" id="search-attributes">
                                            <span class="fa fa-search"></span>
                                            <a href="#" class="fa fa-times hide filter-clear"><span class="sr-only">Effacer filtre</span></a>
                                        </span>
                                    <input type="text" placeholder="Rechercher dans la liste" id="input-attributes" class="form-control live-search" aria-describedby="search-attributes" tabindex="1" />
                                </div>
                            </div>
                            <div id="filter-attributes" class="list-to-filter tree-display">
                                <ul class="list-unstyled">
                                    {foreach $attr as $link}
                                    {if !isset($g) || $g !== $link.id_parent}{$g = $link.id_parent}
                                    {if !$link@first}</ul></li>{/if}
                                <li class="optgroup">
                                    <span class="optgroup-header">{$link.name_parent} (id: {$link.id_parent})</span>
                                    <ul class="list-unstyled">
                                        {/if}
                                        <li class="filter-item items" data-filter="{$link['value_attr']}" data-value="{$link['id_attr_va']}" data-id="{$link['id_attr_va']}">{$link['value_attr']|ucfirst}</li>
                                        {if $link@last}</ul></li>{/if}
                                {/foreach}
                                </ul>
                                <div class="no-search-results">
                                    <div class="alert alert-warning" role="alert"><i class="fa fa-warning margin-right-sm"></i>Aucune entrée pour <strong>'<span></span>'</strong> n'a été trouvée.</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-ph-12 col-md-4">
            <div class="form-group">
                <label for="price_p">{#price#|ucfirst} :</label>
                <input type="text" class="form-control" id="price_p" name="price_p" value="" size="20" />
            </div>
        </div>
    </div>
    <div class="form-group">
        <input type="hidden" name="id" value="{$page.id_product}">
        <button class="btn btn-main-theme" type="submit" name="action" value="add"><span class="fa fa-plus"></span> {#add#}</button>
    </div>
</fieldset>