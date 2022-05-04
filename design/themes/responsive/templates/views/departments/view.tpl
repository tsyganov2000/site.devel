{if $departments}

    {script src="js/tygh/exceptions.js"}
    
    {if !$no_pagination}
        {include "common/pagination.tpl"}
    {/if}

    {if !$show_empty}
        {split data=$departments size=$columns_showcase_departments|default:"2" assign="splitted_departments"}
    {else}
        {split data=$departments size=$columns_showcase_departments|default:"2" assign="splitted_departments" skip_complete=true}
    {/if}

    {math equation="100 / x" x=$columns_showcase_departments|default:"2" assign="cell_width"}

    <div class="grid-list">
        {strip}
            {foreach from=$splitted_departments item="sdepartments"}
                {foreach from=$sdepartments item="department"}
                    <div class="ty-column{$columns_showcase_departments}">
                        {if $department && $department.status === "A"}
                            {assign var="obj_id" value=$department.department_id}
                            {assign var="obj_id_prefix" value="`$obj_prefix``$department.department_id`"}
                            
                            <div class="ty-grid-list__item ty-quick-view-button__wrapper">
                       
                                <div class="ty-grid-list__image">
                                    <a href="{"departments.view_dep?department_id={$department.department_id}"|fn_url}">
                                        {include 
                                            file="common/image.tpl"
                                            no_ids=true
                                            images=$department.main_pair
                                            image_width=$settings.Thumbnails.product_lists_thumbnail_width
                                            image_height=$settings.Thumbnails.product_lists_thumbnail_height
                                            lazy_load=true
                                        }
                                    </a>
                                </div>
                                <div class="ty-grid-list__item-name text-center">
                                    <bdi>
                                        <a href="{"departments.view_dep?department_id={$department.department_id}"|fn_url}" class="product-title" title="{$department.department}">{$department.department}</a>
                                    </bdi>
                                    {$user_info=$department.user_id|fn_get_user_short_info}
                                    <p>{$user_info.firstname} {$user_info.lastname}</p>
                                </div>
                            </div>
                        {/if}
                    </div>
                {/foreach}
            {/foreach}
        {/strip}
    </div>

    {if !$no_pagination}
        {include "common/pagination.tpl"}
    {/if}
{else}
    <p class="no-items">{__("no_data")}</p>
{/if}

{capture name="mainbox_title"}{$title}{/capture}