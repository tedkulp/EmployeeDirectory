<div id="employees">

{foreach from=$first_letters item=letter name=letters}
{anchor anchor=$letter text=$letter}{if !$smarty.foreach.letters.last} | {/if}
{/foreach}

{assign var='first_letter' value=''}

{foreach from=$items item=entry}

  {if $entry->first_letter != $first_letter}
    {assign var='first_letter' value=$entry->first_letter}
    <h3>{$first_letter}<a name="{$first_letter}"></a></h3>
  {/if}

<div class="employee" style="padding-bottom: 10px;">

  {$entry->last_name}, {$entry->first_name}<br />
  {if !empty($entry->position)}{$entry->position}<br />{/if}
  {if !empty($entry->department_name)}{$entry->department_name}<br />{/if}
  {if !empty($entry->office_num)}Office: {$entry->office_num}{/if}{if !empty($entry->office_num) && !empty($entry->extension)}, {/if}{if !empty($entry->extension)}Ext. {$entry->extension}{/if}<br />
  {if !empty($entry->email)}<a href="mailto:{$entry->email}">{$entry->email}</a><br />{/if}
  {if !empty($entry->website)}<a href="{$entry->website}">{$entry->website}</a>{/if}

</div>

{/foreach}

</div>

{if isset($pagecount) && $pagecount gt 1}
{$firstlink} {$prevlink}  {$pagetext} {$curpage} {$oftext} {$pagecount}  {$nextlink} {$lastlink}
{/if}
