{if $bundles}
    <h1>{__("product_bundles.active_bundles")}</h1>
    {foreach $bundles as $bundle}
        {include file="addons/product_bundles/components/bundle_promotion.tpl"}
    {/foreach}
{/if}