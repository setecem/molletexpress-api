{if !isset($page)}
    {assign var="page" value=""}
{/if}

{foreach from=$items.items item="menu"}
    {if isset($items.permission)}
        {if !{can do=$items.permission.action group=$items.permission.group}}
            {continue}
        {/if}
    {/if}
    {if isset($menu.permission)}
        {if !{can do=$menu.permission.action group=$menu.permission.group}}
            {continue}
        {/if}
    {/if}
    {if $menu.childs}
        {assign var="child_active" value=0}
        {foreach from=$menu.childs.items item="child"}
            {if $page eq $child.name}
                {assign var="child_active" value=1}
            {/if}
        {/foreach}
    {/if}
    <div class="col-12 col-md-6 col-lg-3">
        <a href="{if $menu.childs}#{$menu.name}{else}{$menu.link}{/if}" class="btn w-100 btn-primary" {if $menu.childs}class="collapsed" aria-expanded="false" data-bs-toggle="collapse"{/if}>
            <i class="{$menu.icon}"></i>
            <span class="nav_name">{$menu.label} {if $menu.childs}<b class="fa fa-caret-down"></b>{/if}</span>
        </a>
    </div>


{/foreach}
