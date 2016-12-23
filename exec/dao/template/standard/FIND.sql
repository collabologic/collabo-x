SELECT 
{*値リストの部*}
{if count($VLIST)>0}
{foreach from=$VLIST item=VNAME name=loop1}
	`{$VNAME}`{if !$smarty.foreach.loop1.last},{/if}
{/foreach}
{else}
	*
{/if} 
FROM
	{$TABLE}
{*WHEREの部*}
{if count($WHERE)>0}
	WHERE TRUE
	{foreach from=$WHERE item=value key=key}
		{if strstr($key,'_notnull')}
			AND `{$key|replace:'_notnull':''}` IS NOT NULL 
		{elseif strstr($key,'_isnull')}
			AND `{$key|replace:'_isnull':''}` IS NULL 
		{elseif strstr($key,'_greatereq')}
			AND `{$key|replace:'_greatereq':''}` >= '{$value}'
		{elseif strstr($key,'_lesseq')}
			AND `{$key|replace:'_lesseq':''}` <= '{$value}'
		{elseif strstr($key,'_greater')}
			AND `{$key|replace:'_greater':''}` > '{$value}'
		{elseif strstr($key,'_less')}
			AND `{$key|replace:'_less':''}` < '{$value}'
		{elseif strstr($key,'_in')}
			AND `{$key|replace:'_in':''}` IN (
				{foreach from=$value item=inval name=valloop}
					'{$inval}'{if !$smarty.foreach.valloop.last},{/if}
				{/foreach}
			)
			
		{elseif strstr($key,'_noteq')}
			AND `{$key|replace:'_noteq':''}` != '{$value}'
		{elseif strstr($key,'_like')}
			AND `{$key|replace:'_like':''}` LIKE '%{$value}%'
		{elseif strstr($key,'_regexp')}{if $value!=""}
			AND `{$key|replace:'_regexp':''}` REGEXP '{reg_str str=$value}'{/if}
		{elseif $key=="TEXTSRCH"}{if is_array($value)}
			AND TRUE AND (FALSE {foreach from=$value item=item key=key2}
				{foreach from=$item item=str} OR `{$key2}` REGEXP '{reg_str str=$str}' {/foreach}
				{/foreach}){/if}
		{else}
			AND `{$key}`='{$value}'	
		{/if}
	{/foreach}
{/if}

{*ORDER BYの部*}
{if count($ORDER)>0}
ORDER BY
{foreach from=$ORDER item=value name=loop3}
	`{$value.key}` {$value.ascdesc} {if !$smarty.foreach.loop2.last} , {/if}
{/foreach}
{/if} LIMIT 0,1