{** departments section **}

{capture name="mainbox"}

    <form action="{""|fn_url}" method="post" id="departments_form" name="departments_form" enctype="multipart/form-data">
        <input type="hidden" name="fake" value="1" />
        {include "common/pagination.tpl"
            save_current_page=true
            save_current_url=true
            div_id="pagination_contents_departments"
        }

        {$c_url=$config.current_url|fn_query_remove:"sort_by":"sort_order"}

        {$rev=$smarty.request.content_id|default:"pagination_contents_departments"}
        {include_ext file="common/icon.tpl" class="icon-`$search.sort_order_rev`" assign=c_icon}
        {include_ext file="common/icon.tpl" class="icon-dummy" assign=c_dummy}


        {if $departments}
                <div class="table-responsive-wrapper longtap-selection">
                    <table class="table table-middle table--relative table-responsive">
                        <thead>
                            <tr>
                                <th width="6%" class="left mobile-hide">
                                    {include "common/check_items.tpl" class="cm-no-hide-input"}
                                </th>
                                <th width="10%">
                                    <a class="cm-ajax" href="{"`$c_url`&sort_by=position&sort_order=`$search.sort_order_rev`"|fn_url}" data-ca-target-id={$rev}>{__("position")} {if $search.sort_by === "position"} {$c_icon nofilter} {else} {$c_dummy nofilter} {/if}</a>
                                </th>
                                <th width="20%">
                                    <p>{__("logo")}</p>
                                </th>
                                <th>
                                    <a class="cm-ajax" href="{"`$c_url`&sort_by=name&sort_order=`$search.sort_order_rev`"|fn_url}" data-ca-target-id={$rev}>{__("department")} {if $search.sort_by === "name"} {$c_icon nofilter} {else} {$c_dummy nofilter} {/if}</a>
                                </th>
                                <th width="6%" class="mobile-hide">&nbsp;</th>
                                <th width="10%" class="right">
                                    <a class="cm-ajax" href="{"`$c_url`&sort_by=status&sort_order=`$search.sort_order_rev`"|fn_url}" data-ca-target-id={$rev}>{__("status")}{if $search.sort_by === "status"}{$c_icon nofilter}{else}{$c_dummy nofilter}{/if}</a>
                                </th>
                            </tr>
                        </thead>
                        {foreach from=$departments item=department}

                            <tr class="cm-row-status-{$department.status|lower} cm-longtap-target">

                                {$allow_save=$department|fn_allow_save_object:"departments"}

                                {if $allow_save}
                                    {$no_hide_input="cm-no-hide-input"}
                                {else}
                                    {$no_hide_input=""}
                                {/if}

                                <td width="6%" class="left mobile-hide">
                                    <input type="checkbox" name="departments_ids[]" value="{$department.department_id}" class="cm-item {$no_hide_input}" />
                                </td>
                                <td>
                                    <input type="text" name="departments_data[{$department.department_id}][position]" value="{$department.position}" size="3" class="input-micro">
                                </td>
                                <td class="products-list__image">
                                    {include "common/image.tpl"
                                        image=$department.main_pair.icon|default:$department.main_pair.detailed
                                        image_id=$department.main_pair.image_id
                                        image_width=$settings.Thumbnails.product_lists_thumbnail_width
                                        image_height=$settings.Thumbnails.product_lists_thumbnail_height
                                        href="departments.update?department_id=`$department.department_id`"|fn_url
                                        image_css_class="products-list__image--img"
                                        link_css_class="products-list__image--link"
                                    }
                                </td>
                                <td class="{$no_hide_input}" data-th="{__("department")}">
                                    <a class="row-status" href="{"departments.update?department_id=`$department.department_id`"|fn_url}">{$department.department}</a>
                                </td>
                                <td width="6%" class="mobile-hide">
                                    {capture name="tools_list"}
                                        <li>{btn type="list" text=__("edit") href="departments.update?department_id=`$department.department_id`"}</li>
                                        {if $allow_save}
                                            <li>{btn type="list" class="cm-confirm" text=__("delete") href="departments.delete_department?department_id=`$department.department_id`" method="POST"}</li>
                                        {/if}
                                    {/capture}
                                    <div class="hidden-tools">
                                        {dropdown content=$smarty.capture.tools_list}
                                    </div>
                                </td>
                                <td class="right" data-th"{__("status")}">
                                    {include "common/select_popup.tpl" 
                                        id=$department.department_id
                                        status=$department.status 
                                        hidden=true 
                                        object_id_name="department_id" 
                                        table="departments" 
                                        popup_additional_class="`$no_hide_input` dropleft"
                                    }
                                </td>
                            </tr>
                        {/foreach}
                    </table>
                </div>
                {include "common/context_menu_wrapper.tpl"
                    form="departments_form"
                    object="departments"
                    items=$smarty.capture.departments_table
                }
        {else}
            <p class="no-items">{__("no_data")}</p>
        {/if}

        {include "common/pagination.tpl" div_id="pagination_contents_departments"}

        {capture name="buttons"}
            {capture name="tools_list"}
                {if $departments}
                    <li>{btn type="delete_selected" dispatch="dispatch[departments.delete_departments]" form="departments_form"}</li>
                {/if}
            {/capture}
            {dropdown content=$smarty.capture.tools_list class="mobile-hide"}
            {include "buttons/save.tpl" 
                but_name="dispatch[departments.update_departments]" 
                but_role="action" 
                but_target_form="departments_form" 
                but_meta="cm-submit"
            }
        {/capture}
        {capture name="adv_buttons"}
            {include "common/tools.tpl" 
                tool_href="departments.add" 
                prefix="top" 
                hide_tools="true" 
                title=__("add_new_dep") 
                icon="icon-plus"
            }
        {/capture}

    </form>
{/capture}

{capture name="sidebar"}
    {include "common/saved_search.tpl" dispatch="departments.manage" view_type="departments"}
    {include "views/departments/components/departments_search_form.tpl" dispatch="departments.manage"}
{/capture}


{include 
    file="common/mainbox.tpl" 
    title={__("departments")}
    content=$smarty.capture.mainbox 
    buttons=$smarty.capture.buttons
    adv_buttons=$smarty.capture.adv_buttons 
    select_languages=$select_languages
    sidebar=$smarty.capture.sidebar
}

{** ad section **}
