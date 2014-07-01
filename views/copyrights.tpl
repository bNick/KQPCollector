<style type="text/css">
.icons a.edit { background-image: url(design/images/pencil.png); }
</style>

{* Вкладки *}
{capture name=tabs}
    <li {if $status===0}class="active"{/if}><a href="{url module=CopyrightsAdmin status=0 keyword=null id=null page=null}">Постановка</a></li>
	<li {if $status==1}class="active"{/if}><a href="{url module=CopyrightsAdmin status=1 keyword=null id=null page=null}">Приём</a></li>
{/capture}


{* Title *}
{$meta_title='Копирайты' scope=parent}

{* Поиск *}
{if $copyrights || $keyword}
<form method="get">
<div id="search">
	<input type="hidden" name="module" value='CopyrightsAdmin'>
	<input class="search" type="text" name="keyword" value="{$keyword|escape}" />
	<input class="search_button" type="submit" value=""/>
</div>
</form>
{/if}


{* Заголовок *}
<div id="header">
	{if $keyword && $copyrights_count}
	<h1>{$copyrights_count|plural:'Нашелся':'Нашлось':'Нашлись'} {$copyrights_count} {$copyrights_count|plural:'копирайт':'копирайтов':'копирайта'}</h1> 
	{elseif !$type}
	<h1>{$copyrights_count} {$copyrights_count|plural:'копирайт':'копирайтов':'копирайта'}</h1> 
	{elseif $type=='product'}
	<h1>{$copyrights_count} {$copyrights_count|plural:'копирайт':'копирайтов':'копирайта'} к товарам</h1> 
	{elseif $type=='blog'}
	<h1>{$copyrights_count} {$copyrights_count|plural:'копирайт':'копирайтов':'копирайта'} к записям в блоге</h1> 
	{/if}
</div>	


{if $copyrights}
<div id="main_list">
	
	<!-- Листалка страниц -->
	{include file='pagination.tpl'}	
	<!-- Листалка страниц (The End) -->
	
	<form id="list_form1" method="post">
	<input type="hidden" name="session_id" value="{$smarty.session.id}">

		<div id="list" class="sortable">
			{foreach $copyrights as $copyright}
			<div class="{if !$copyright->approved}unapproved{/if} row">
		 		<div class="checkbox cell">
					<input type="checkbox" name="check[]" value="{$copyright->id}"/>				
				</div>
				<div class="name cell">
					<div class="copyright_name">
    					<div class="type" hidden="true">{$copyright->type}</div>
    					<div class="object_id" hidden="true">{$copyright->object_id}</div>
    					<div class="email" hidden="true">{$copyright->email}</div>
    					ID {$copyright->id}: <a class="to-site" href="../{$copyright->url}?rabota=1" title="Предпросмотр в новом окне" target="_blank">{$base_url}{$copyright->url}</a><br />
                        <a class="approve" href="#">Одобрить</a><a class="send" href="#">Отправить на доработку</a><br />
					</div>
					{*<div class="copyright_text">
					{$copyright->text|escape|nl2br}
					</div>*}
                    <div class="more">Подробнее</div>
                    <div class="more-content" style="display: none;">
                        Статус решения задачи: {$copyright->solution_stat|escape}<br />
    					Стоимость за задачу: {$copyright->task_cost|escape}<br />
    					Статус оплаты: {$copyright->payment_stat|escape}<br />
    					Title: <span class="title">{$copyright->title|escape}</span><br />
    					Keywords: <span class="keywords">{$copyright->keywords|escape}</span><br />
    					Description: <span class="description">{$copyright->description|escape}</span><br />
                        
                        <div class="copyright_body">Описание: <br />{$copyright->text|escape|nl2br}</div>
			
            			{if $copyright->otvet}
            			<div class="copyright_admint">Администрация:</div>
            			<div class="copyright_admin">
            				{$copyright->otvet|escape|nl2br}
            			</div>
            			{/if}
    
    					<div class="copyright_info">
        					Копирайт оставлен {$copyright->date|date} в {$copyright->date|time}
        					{if $copyright->type == 'product'}
        					к задаче <a target="_blank" href="{$config->root_url}/products/{$copyright->product->url}#copyright_{$copyright->id}">{$copyright->product->name}</a>
        					{elseif $copyright->type == 'blog'}
        					к статье <a target="_blank" href="{$config->root_url}/blog/{$copyright->post->url}#copyright_{$copyright->id}">{$copyright->post->name}</a>
        					{/if}
    					</div>
                    </div>
				</div>
                
				<div class="icons cell">
<a style="margin-right: 10px;" class="edit" title="Редактировать" href="{url module=CopyrightAdmin id=$copyright->id return=$smarty.server.REQUEST_URI}"></a>
					<a class="delete" title="Удалить" href="#"></a>
				</div>
				<div class="clear"></div>
			</div>
			{/foreach}
		</div>
	
		<div id="action">
		Выбрать <label id="check_all" class="dash_link">все</label> или <label id="check_unapproved" class="dash_link">ожидающие</label>
	
		<span id="select">
		<select name="action">
			<option value="approve">Одобрить</option>
			<option value="delete">Удалить</option>
		</select>
		</span>
	
		<input id="apply_action" class="button_green" type="submit" value="Применить">

	</div>
	</form>
	
	<!-- Листалка страниц -->
	{include file='pagination.tpl'}	
	<!-- Листалка страниц (The End) -->
		
</div>
{else}
Нет копирайтов
{/if}

<!-- Меню -->
<div id="right_menu">
	
	<!-- Категории товаров -->
	<ul>
	<li>
		<div class="onoffswitch" name="hello">
    <input type="checkbox" name="onoffswitch" class="onoffswitch-checkbox" id="myonoffswitch" {$check}>
    <label class="onoffswitch-label" for="myonoffswitch">
        <div class="onoffswitch-inner"></div>
        <div class="onoffswitch-switch"></div>
    </label>
</div>
</li>
	<!--<li {if !$type}class="selected"{/if}><a href="{url type=null}">Все комментарии</a></li>
	</ul>
	<ul>
		<li {if $type == 'product'}class="selected"{/if}><a href='{url keyword=null type=product}'>К товарам</a></li>
		<li {if $type == 'blog'}class="selected"{/if}><a href='{url keyword=null type=blog}'>К блогу</a></li>
	</ul>
	<!-- Категории товаров (The End)-->
	
</div>
<!-- Меню  (The End) -->

{literal}
<script>

$(function() {
 
	$("#myonoffswitch").click(function() {
	var attr = $(this).attr('checked');
		if (typeof attr !== 'undefined' && attr !== false) 
		{
		window.location.href = "/scpro/index.php?module=KQPCollector&type=opl";
		}
		else
		{
		window.location.href = "/scpro/index.php?module=KQPCollector&type=notopl";
		}
	});

	// Раскраска строк
	function colorize()
	{
		$("#list div.row:even").addClass('even');
		$("#list div.row:odd").removeClass('even');
	}
	// Раскрасить строки сразу
	colorize();
	
	// Выделить все
	$("#check_all").click(function() {
		$('#list input[type="checkbox"][name*="check"]').attr('checked', $('#list input[type="checkbox"][name*="check"]:not(:checked)').length>0);
	});	

	// Выделить ожидающие
	$("#check_unapproved").click(function() {
		$('#list input[type="checkbox"][name*="check"]').attr('checked', false);
		$('#list .unapproved input[type="checkbox"][name*="check"]').attr('checked', true);
	});	

	// Удалить 
	$("a.delete").click(function() {
		$('#list input[type="checkbox"][name*="check"]').attr('checked', false);
		$(this).closest(".row").find('input[type="checkbox"][name*="check"]').attr('checked', true);
		$(this).closest("form").find('select[name="action"] option[value=delete]').attr('selected', true);
		$(this).closest("form").submit();
	});
	
	// Одобрить
	$("a.approve").click(function() {
		var line        = $(this).closest(".row");
		var id          = line.find('input[type="checkbox"][name*="check"]').val();
		$.ajax({
			type: 'POST',
			url: 'ajax/update_object.php',
			data: {'object': 'copyright', 'id': id, 'values':{'approved': '1'}, 'session_id': '{/literal}{$smarty.session.id}{literal}'},
			success: function(data){
				line.removeClass('unapproved');
			},
			dataType: 'json'
		});	
		return false;	
	});
	
	$("form#list_form").submit(function() {
		if($('#list_form select[name="action"]').val()=='delete' && !confirm('Подтвердите удаление'))
			return false;	
	});	
	$("a.approve").click(function() {
		var line        = $(this).closest(".row");
		var id          = line.find('input[type="checkbox"][name*="check"]').val();
		$.ajax({
			type: 'POST',
			url: 'ajax/update_object.php',
			data: {'object': 'copyright', 'id': id, 'values':{'solution_stat': 'Решено'}, 'session_id': '{/literal}{$smarty.session.id}{literal}'},
			success: function(data){
				line.removeClass('unapproved');
			},
			dataType: 'json'
		});	
		return false;	
	});
	$("a.approve").click(function() {
		var line        = $(this).closest(".row");
		var id          = line.find('input[type="checkbox"][name*="check"]').val();
		var task_desk = $('.copyright_body').html()
		var email = $('.email').html()
		var type = $('.type').html()
		var object_id = $('.object_id').html()
		$.ajax({
			type: 'POST',
			url: 'ajax/update_object.php',
			data: {'object': type, 'id': object_id, 'values':{'body': email + task_desk}, 'session_id': '{/literal}{$smarty.session.id}{literal}'},
			success: function(data){
				line.removeClass('unapproved');
			},
			dataType: 'json'
		});	
		return false;	
	});
	$("a.approve").click(function() {
		var line        = $(this).closest(".row");
		var id          = line.find('input[type="checkbox"][name*="check"]').val();
		var title = $('.title').html()
		var type = $('.type').html()
		var object_id = $('.object_id').html()
		$.ajax({
			type: 'POST',
			url: 'ajax/update_object.php',
			data: {'object': type, 'id': object_id, 'values':{'meta_title': title}, 'session_id': '{/literal}{$smarty.session.id}{literal}'},
			success: function(data){
				line.removeClass('unapproved');
			},
			dataType: 'json'
		});	
		return false;	
	});
	$("a.approve").click(function() {
		var line        = $(this).closest(".row");
		var id          = line.find('input[type="checkbox"][name*="check"]').val();
		var keywords = $('.keywords').html()
		var type = $('.type').html()
		var object_id = $('.object_id').html()
		$.ajax({
			type: 'POST',
			url: 'ajax/update_object.php',
			data: {'object': type, 'id': object_id, 'values':{'meta_keywords': keywords}, 'session_id': '{/literal}{$smarty.session.id}{literal}'},
			success: function(data){
				line.removeClass('unapproved');
			},
			dataType: 'json'
		});	
		return false;	
	});
	$("a.approve").click(function() {
		var line        = $(this).closest(".row");
		var id          = line.find('input[type="checkbox"][name*="check"]').val();
		var description = $('.description').html()
		var type = $('.type').html()
		var object_id = $('.object_id').html()
		$.ajax({
			type: 'POST',
			url: 'ajax/update_object.php',
			data: {'object': type, 'id': object_id, 'values':{'meta_description': description}, 'session_id': '{/literal}{$smarty.session.id}{literal}'},
			success: function(data){
				line.removeClass('unapproved');
			},
			dataType: 'json'
		});	
		return false;	
	});
	$("a.send").click(function() {
		var line        = $(this).closest(".row");
		var id          = line.find('input[type="checkbox"][name*="check"]').val();
		$.ajax({
			type: 'POST',
			url: 'ajax/update_object.php',
			data: {'object': 'copyright', 'id': id, 'values':{'solution_stat': 'В очереди'}, 'session_id': '{/literal}{$smarty.session.id}{literal}'},
			dataType: 'json'
		});	
		return false;	
	});
	$("a.send").click(function() {
		var line        = $(this).closest(".row");
		var id          = line.find('input[type="checkbox"][name*="check"]').val();
		$.ajax({
			type: 'POST',
			url: 'ajax/update_object.php',
			data: {'object': 'copyright', 'id': id, 'values':{'approved': '0'}, 'session_id': '{/literal}{$smarty.session.id}{literal}'},
			dataType: 'json'
		});	
		return false;	
	});

    $('.more').click(function(){
        $(this).hide().next('.more-content').show();
    });
 	
});

</script>
{/literal}