UPDATE {$TABLE}
SET 
{foreach from=$VALUES item=value key=key name=valloop}
	`{$key}` = '{$value}'{if !$smarty.foreach.valloop.last},{/if}
{/foreach}
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
		{else}
			AND `{$key}`='{$value}'	
		{/if}
	{/foreach}
{/if}