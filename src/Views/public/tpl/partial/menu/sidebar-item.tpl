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
    {assign var="child_permisions" value=1}
    {if $menu.childs}
        {assign var="child_active" value=0}
        {foreach from=$menu.childs.items item="child"}
            {if $page eq $child.name}
                {assign var="child_active" value=1}
            {/if}
        {/foreach}

        {assign var="child_permisions" value=0}
        {foreach from=$menu.childs.items item="child"}
            {if isset($child.permission)}
                {if {can do=$child.permission.action group=$child.permission.group}}
                    {assign var="child_permisions" value=1}
                {/if}
            {/if}
        {/foreach}
    {/if}
    {if $child_permisions}
        <li  data-original-title="{$menu.label}">
            <a href="{if $menu.childs}#{$menu.name}{else}{$menu.link}{/if}" class="nav_link {if $menu.childs}{if $child_active}active{/if}{else}{if $page == $menu.name}active{/if}{/if}" {if $menu.childs}class="collapsed" aria-expanded="{if $menu.childs}{if $child_active}true{else}false{/if}{else}{if $page == $menu.name}active{else}false{/if}{/if}" data-bs-toggle="collapse"{/if}>
                <i class="{$menu.icon} nav_icon"></i>
                <span class="nav_name">{$menu.label} {if $menu.childs}<b class="fa fa-caret-down"></b>{/if}</span>
            </a>
            {if $menu.childs}
                <div class="collapse {if $menu.childs}{if $child_active}show{/if}{else}{if $page == $menu.name}show{/if}{/if}" id="{$menu.name}">
                    <ul class="nav">
                        {include file="partial/menu/sidebar-item.tpl" items=$menu.childs}
                    </ul>
                </div>
            {/if}
        </li>
    {/if}
{/foreach}
