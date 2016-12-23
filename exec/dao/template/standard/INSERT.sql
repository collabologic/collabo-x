INSERT INTO {$TABLE} 
{*値リストの部*}
(
{foreach from=$VALUES item=value key=key name=loop1}
	`{$key}`{if !$smarty.foreach.loop1.last},{/if}
{/foreach}
)
VALUES(
{foreach from=$VALUES item=value key=key name=loop1}
	'{$value}'{if !$smarty.foreach.loop1.last},{/if}
{/foreach}
)