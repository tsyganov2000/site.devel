{if $bundles}
    {foreach $bundles as $key => $bundle}
        <h3 class="ty-product-bundle__header ty-subheader">{$bundle.storefront_name}</h3>
        {include file="addons/product_bundles/components/bundle_form.tpl"}
    {/foreach}
{/if}
