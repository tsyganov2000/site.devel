{if $department_data.department}
    <div class="ty-feature">
        {if $department_data.main_pair}
            <div class="ty-feature__image">
                {include 
                    file="common/image.tpl" 
                    images=$department_data.main_pair
                    image_width=$settings.Thumbnails.product_lists_thumbnail_width
                    image_height=$settings.Thumbnails.product_lists_thumbnail_height
                }
            </div>
        {/if}
        <div class="ty-feature__description ty-wysiwyg-content">
            {$department_data.description nofilter}
        </div>
        <div class="ty-feature">
            <strong>{__("head_dep")}: </strong>
            {if $department_data.user_id}
                {$user_info=$department_data.user_id|fn_get_user_short_info}
                <p>{$user_info.firstname} {$user_info.lastname}</p>
            {else}
            {__(text_no_dep)}
            {/if}
        </div>
    </div>

    
    <div class="table-responsive-wrapper">
        <strong>{__("members_dep")}:</strong>
        <table width="100%" class="table table-middle table--relative table-responsive table-responsive-w-titles">
            <tbody>
                {if $department_data.member_user_ids}
                    {foreach from=$department_data.member_user_ids item="user"}
                        {$user_info=$user|fn_get_user_short_info}
                        <tr>
                            <td>{$user_info.firstname} {$user_info.lastname} {$user_info.email}</td>
                        </tr>
                    {/foreach}
                {else}
                    <tr>
                        <td>{__("text_no_members_dep")}</td>
                    </tr>
                {/if}
            </tbody>
        </table>
    </div>
{/if}
{capture name="mainbox_title"}{$department_data.department nofilter}{/capture}
    