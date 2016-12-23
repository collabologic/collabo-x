SELECT 
{*値リストの部*}
{if count($VLIST)>0}
DISTINCT
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
		{elseif strstr($key,'_aiueo')}{if $value!=""}
			AND FALSE OR
			{if $value==a}
				( 	{$key|replace:'_aiueo':''} LIKE 'あ%' 
				OR	{$key|replace:'_aiueo':''} LIKE 'い%' 
				OR	{$key|replace:'_aiueo':''} LIKE 'う%' 
				OR	{$key|replace:'_aiueo':''} LIKE 'え%' 
				OR	{$key|replace:'_aiueo':''} LIKE 'お%' 
				OR	{$key|replace:'_aiueo':''} LIKE 'ア%' 
				OR	{$key|replace:'_aiueo':''} LIKE 'イ%' 
				OR	{$key|replace:'_aiueo':''} LIKE 'ウ%' 
				OR	{$key|replace:'_aiueo':''} LIKE 'エ%' 
				OR	{$key|replace:'_aiueo':''} LIKE 'オ%' 
				)
			{elseif $value==k}
				( 	{$key|replace:'_aiueo':''} LIKE 'か%' 
				OR	{$key|replace:'_aiueo':''} LIKE 'き%' 
				OR	{$key|replace:'_aiueo':''} LIKE 'く%' 
				OR	{$key|replace:'_aiueo':''} LIKE 'け%' 
				OR	{$key|replace:'_aiueo':''} LIKE 'こ%' 
				OR	{$key|replace:'_aiueo':''} LIKE 'カ%' 
				OR	{$key|replace:'_aiueo':''} LIKE 'キ%' 
				OR	{$key|replace:'_aiueo':''} LIKE 'ク%' 
				OR	{$key|replace:'_aiueo':''} LIKE 'ケ%' 
				OR	{$key|replace:'_aiueo':''} LIKE 'コ%' 
				OR	{$key|replace:'_aiueo':''} LIKE 'が%' 
				OR	{$key|replace:'_aiueo':''} LIKE 'ぎ%' 
				OR	{$key|replace:'_aiueo':''} LIKE 'ぐ%' 
				OR	{$key|replace:'_aiueo':''} LIKE 'げ%' 
				OR	{$key|replace:'_aiueo':''} LIKE 'ご%' 
				OR	{$key|replace:'_aiueo':''} LIKE 'ガ%' 
				OR	{$key|replace:'_aiueo':''} LIKE 'ギ%' 
				OR	{$key|replace:'_aiueo':''} LIKE 'グ%' 
				OR	{$key|replace:'_aiueo':''} LIKE 'ゲ%' 
				OR	{$key|replace:'_aiueo':''} LIKE 'ゴ%' 
				)
			{elseif $value==s}
				( 	{$key|replace:'_aiueo':''} LIKE 'さ%' 
				OR	{$key|replace:'_aiueo':''} LIKE 'し%' 
				OR	{$key|replace:'_aiueo':''} LIKE 'す%' 
				OR	{$key|replace:'_aiueo':''} LIKE 'せ%' 
				OR	{$key|replace:'_aiueo':''} LIKE 'そ%' 
				OR	{$key|replace:'_aiueo':''} LIKE 'サ%' 
				OR	{$key|replace:'_aiueo':''} LIKE 'シ%' 
				OR	{$key|replace:'_aiueo':''} LIKE 'ス%' 
				OR	{$key|replace:'_aiueo':''} LIKE 'セ%' 
				OR	{$key|replace:'_aiueo':''} LIKE 'ソ%'
				OR	{$key|replace:'_aiueo':''} LIKE 'ざ%' 
				OR	{$key|replace:'_aiueo':''} LIKE 'じ%' 
				OR	{$key|replace:'_aiueo':''} LIKE 'ず%' 
				OR	{$key|replace:'_aiueo':''} LIKE 'ぜ%' 
				OR	{$key|replace:'_aiueo':''} LIKE 'ぞ%' 
				OR	{$key|replace:'_aiueo':''} LIKE 'ザ%' 
				OR	{$key|replace:'_aiueo':''} LIKE 'ジ%' 
				OR	{$key|replace:'_aiueo':''} LIKE 'ズ%' 
				OR	{$key|replace:'_aiueo':''} LIKE 'ゼ%' 
				OR	{$key|replace:'_aiueo':''} LIKE 'ゾ%' 
				)
				{elseif $value==t}
				( 	{$key|replace:'_aiueo':''} LIKE 'た%' 
				OR	{$key|replace:'_aiueo':''} LIKE 'ち%' 
				OR	{$key|replace:'_aiueo':''} LIKE 'つ%' 
				OR	{$key|replace:'_aiueo':''} LIKE 'て%' 
				OR	{$key|replace:'_aiueo':''} LIKE 'と%' 
				OR	{$key|replace:'_aiueo':''} LIKE 'タ%' 
				OR	{$key|replace:'_aiueo':''} LIKE 'チ%' 
				OR	{$key|replace:'_aiueo':''} LIKE 'ツ%' 
				OR	{$key|replace:'_aiueo':''} LIKE 'テ%' 
				OR	{$key|replace:'_aiueo':''} LIKE 'ト%' 
				OR	{$key|replace:'_aiueo':''} LIKE 'だ%' 
				OR	{$key|replace:'_aiueo':''} LIKE 'ぢ%' 
				OR	{$key|replace:'_aiueo':''} LIKE 'づ%' 
				OR	{$key|replace:'_aiueo':''} LIKE 'で%' 
				OR	{$key|replace:'_aiueo':''} LIKE 'ど%' 
				OR	{$key|replace:'_aiueo':''} LIKE 'ダ%' 
				OR	{$key|replace:'_aiueo':''} LIKE 'ヂ%' 
				OR	{$key|replace:'_aiueo':''} LIKE 'ヅ%' 
				OR	{$key|replace:'_aiueo':''} LIKE 'デ%' 
				OR	{$key|replace:'_aiueo':''} LIKE 'ド%' 
				)
				{elseif $value==n}
				( 	{$key|replace:'_aiueo':''} LIKE 'な%' 
				OR	{$key|replace:'_aiueo':''} LIKE 'に%' 
				OR	{$key|replace:'_aiueo':''} LIKE 'ぬ%' 
				OR	{$key|replace:'_aiueo':''} LIKE 'ね%' 
				OR	{$key|replace:'_aiueo':''} LIKE 'の%' 
				OR	{$key|replace:'_aiueo':''} LIKE 'ナ%' 
				OR	{$key|replace:'_aiueo':''} LIKE 'ニ%' 
				OR	{$key|replace:'_aiueo':''} LIKE 'ヌ%' 
				OR	{$key|replace:'_aiueo':''} LIKE 'ネ%' 
				OR	{$key|replace:'_aiueo':''} LIKE 'ノ%' 
				)
				{elseif $value==h}
				( 	{$key|replace:'_aiueo':''} LIKE 'は%' 
				OR	{$key|replace:'_aiueo':''} LIKE 'ひ%' 
				OR	{$key|replace:'_aiueo':''} LIKE 'ふ%' 
				OR	{$key|replace:'_aiueo':''} LIKE 'へ%' 
				OR	{$key|replace:'_aiueo':''} LIKE 'ほ%' 
				OR	{$key|replace:'_aiueo':''} LIKE 'ハ%' 
				OR	{$key|replace:'_aiueo':''} LIKE 'ヒ%' 
				OR	{$key|replace:'_aiueo':''} LIKE 'フ%' 
				OR	{$key|replace:'_aiueo':''} LIKE 'ヘ%' 
				OR	{$key|replace:'_aiueo':''} LIKE 'ホ%' 
				OR	{$key|replace:'_aiueo':''} LIKE 'ば%' 
				OR	{$key|replace:'_aiueo':''} LIKE 'び%' 
				OR	{$key|replace:'_aiueo':''} LIKE 'ぶ%' 
				OR	{$key|replace:'_aiueo':''} LIKE 'べ%' 
				OR	{$key|replace:'_aiueo':''} LIKE 'ぼ%' 
				OR	{$key|replace:'_aiueo':''} LIKE 'バ%' 
				OR	{$key|replace:'_aiueo':''} LIKE 'ビ%' 
				OR	{$key|replace:'_aiueo':''} LIKE 'ブ%' 
				OR	{$key|replace:'_aiueo':''} LIKE 'ベ%' 
				OR	{$key|replace:'_aiueo':''} LIKE 'ボ%' 
				)
				{elseif $value==m}
				( 	{$key|replace:'_aiueo':''} LIKE 'ま%' 
				OR	{$key|replace:'_aiueo':''} LIKE 'み%' 
				OR	{$key|replace:'_aiueo':''} LIKE 'む%' 
				OR	{$key|replace:'_aiueo':''} LIKE 'め%' 
				OR	{$key|replace:'_aiueo':''} LIKE 'も%' 
				OR	{$key|replace:'_aiueo':''} LIKE 'マ%' 
				OR	{$key|replace:'_aiueo':''} LIKE 'ミ%' 
				OR	{$key|replace:'_aiueo':''} LIKE 'ム%' 
				OR	{$key|replace:'_aiueo':''} LIKE 'メ%' 
				OR	{$key|replace:'_aiueo':''} LIKE 'モ%' 
				)
				{elseif $value==y}
				( 	{$key|replace:'_aiueo':''} LIKE 'や%' 
				OR	{$key|replace:'_aiueo':''} LIKE 'ゆ%' 
				OR	{$key|replace:'_aiueo':''} LIKE 'よ%' 
				OR	{$key|replace:'_aiueo':''} LIKE 'ヤ%' 
				OR	{$key|replace:'_aiueo':''} LIKE 'ユ%' 
				OR	{$key|replace:'_aiueo':''} LIKE 'ヨ%' 
				)
				{elseif $value==r}
				( 	{$key|replace:'_aiueo':''} LIKE 'ら%' 
				OR	{$key|replace:'_aiueo':''} LIKE 'り%' 
				OR	{$key|replace:'_aiueo':''} LIKE 'る%' 
				OR	{$key|replace:'_aiueo':''} LIKE 'れ%' 
				OR	{$key|replace:'_aiueo':''} LIKE 'ろ%' 
				OR	{$key|replace:'_aiueo':''} LIKE 'ラ%' 
				OR	{$key|replace:'_aiueo':''} LIKE 'リ%' 
				OR	{$key|replace:'_aiueo':''} LIKE 'ル%' 
				OR	{$key|replace:'_aiueo':''} LIKE 'レ%' 
				OR	{$key|replace:'_aiueo':''} LIKE 'ロ%' 
				)
				{elseif $value==w}
				( 	{$key|replace:'_aiueo':''} LIKE 'わ%' 
				OR	{$key|replace:'_aiueo':''} LIKE 'ワ%' 
				)
			{/if}{/if}			
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
{if isset($ORDER)}
ORDER BY 
	`{$ORDER}` {$ASCDESC}
{/if}

{*LIMIT句の部*}
{if isset($LIMIT) }
	Limit {$OFFSET},{$LIMIT}
{/if}