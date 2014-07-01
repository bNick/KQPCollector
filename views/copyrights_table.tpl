<link href="design/js/jquery.tablesorter/themes/blue/style.css" rel="stylesheet" type="text/css" />
<style type="text/css">
.icons a.edit { background-image: url(design/images/pencil.png); }
#middle {
    margin-left: -128px;
    width: 1193px;
}
</style>

{* Вкладки *}
{capture name=tabs}
    <li class="active"><a href="{url module=CopyrightsAdmin status=0 keyword=null id=null page=null}">Постановка</a></li>
	<li><a href="{url module=CopyrightsAdmin status=1 keyword=null id=null page=null}">Приём</a></li>
{/capture}


{if isset($msg)}
<p style="color: blue;">{$msg}</p>
{/if}
{* Title *}
{$meta_title='Копирайты' scope=parent}

<div id="main_list" class="full-width">
    <form id="list_form" method="post">
        <input type="hidden" name="session_id" value="{$smarty.session.id}">
        <input type="hidden" name="menu_id" value="{$menu->id}">
        {function name=page_row}
            <tr class="{if !$page->visible}invisible{/if} row">
                <td class="checkbox cell">
                    <input class="cb-pages" type="checkbox" name="check[{$type}][]" value="{$page->id}"/>
                </td>
                <td class="number_id cell">
                    {$page->id}
                </td>
                <td class="url cell">
                    <a href="../{$path}{$page->url}-rabota" title="Предпросмотр в новом окне" target="_blank">{$base_url}{$path}{$page->url}</a>
                </td>
                <td class="number cell">
                    {$page->KN}
                </td>
                <td class="number cell">
                    {$page->KV}
                </td>
                <td class="mid-cb cell">
                    <input class="validate" name="data[{$page->id}][RST]" value="1" type="checkbox" {if $page->RST == 1} checked{/if}/>
                </td>
                <td class="mid-input cell">
                    <input class="cb-for-input validate" name="data[{$page->id}][RNT_int]" value="1" type="checkbox" {if $page->RNT_int == 1} checked{/if}/>
                    <input{if $page->RNT_int != 1} style="display:none;"{/if} name="data[{$page->id}][RNT]" type="text" value="{$page->RNT}"/>
                </td>
                <td class="mid-input cell">
                    <input class="cb-for-input validate" name="data[{$page->id}][K_int]" value="1" type="checkbox" {if $page->K_int == 1} checked{/if}/>
                    <input{if $page->K_int != 1} style="display:none;"{/if} name="data[{$page->id}][K_meta]" type="text" value="{$page->K_meta}"/>
                </td>
                <td class="mid-input cell">
                    <input class="cb-for-input validate" name="data[{$page->id}][OS_int]" value="1" type="checkbox" {if $page->OS_int == 1} checked{/if}/>
                    <input{if $page->OS_int != 1} style="display:none;"{/if} name="data[{$page->id}][OS]" type="text" value="{$page->OS}"/>
                </td>
                <td class="mid-input cell">
                    <input class="cb-for-input validate" name="data[{$page->id}][PG_int]" value="1" type="checkbox" {if $page->PG_int == 1} checked{/if}/>
                    <input{if $page->PG_int != 1} style="display:none;"{/if} name="data[{$page->id}][PG]" type="text" value="{$page->PG}"/>
                </td>
                <td class="mini-select cell">
                    {if in_array($page->text_count, array(0, 500, 1000, 1500, 2000, 2500))}
                    {$not_other = 1}
                    {else}
                    {$not_other = 0}
                    {/if}
                    <select class="sel-with-other" name="data[{$page->id}][text_count]">
                        <option{if $page->text_count == 500} selected{/if}>500</option>
                        <option{if $page->text_count == 1000} selected{/if}>1000</option>
                        <option{if $page->text_count == 1500} selected{/if}>1500</option>
                        <option{if $page->text_count == 2000} selected{/if}>2000</option>
                        <option{if $page->text_count == 2500} selected{/if}>2500</option>
                        <option{if !$not_other} selected{/if} value="other">Ввести вручную</option>
                    </select>
                    <input{if $not_other} style="display:none;"{/if} name="data[{$page->id}][text_count_other]" type="text" value="{$page->text_count}"/>
                </td>
                <td class="mini-select cell">
                    {if in_array($page->task_cost, array(0, 50, 100, 150))}
                    {$not_price = 1}
                    {else}
                    {$not_price = 0}
                    {/if}
                    <select class="sel-with-other" name="data[{$page->id}][task_cost]">
                        <option{if $page->task_cost == 50} selected{/if}>50</option>
                        <option{if $page->task_cost == 100} selected{/if}>100</option>
                        <option{if $page->task_cost == 150} selected{/if}>150</option>
                        <option{if !$not_price} selected{/if} value="other">Ввести вручную</option>
                    </select>
                    <input{if $not_price} style="display:none;"{/if} name="data[{$page->id}][task_cost_other]" type="text" value="{$page->task_cost}"/>
                </td>
                <td class="input cell">
                    <input class="keywords" name="data[{$page->id}][keywords]" type="text" value="{$page->keywords}"/>
                </td>
                <td class="mini-input cell">
                    <input class="key_count" name="data[{$page->id}][key_count]" type="text" value="{$page->key_count}"/>
                </td>
                <td class="textarea cell">
                    <textarea name="data[{$page->id}][task_desc]">{$page->task_desc}</textarea>
                </td>
                <td class="date cell">{$page->update_time}</td>
            </tr>
        {/function} 

        <hr/>
        <div id="list" class="sortable" data-category-id="0">
            
            <table id="CR-table" class="tablesorter">
                <thead>
                    <tr class="row head-row">
                        <th class="checkbox cell"></th>
                        <th class="number_id cell" title="ID страницы">ID</th>
                        <th class="url cell" title="Ссылка на страницу">Ссылка</th>
                        <th class="number cell" title="Контент невидимый">КН</th>
                        <th class="number cell" title="Контент видимый">КВ</th>
                        <th class="mid-cb cell" title="Контент видимый">РСТ</th>
                        <th class="mid-input cell" title="Рерайт нового текста">РНТ</th>
                        <th class="mid-input cell" title="Копирайтинг с МЕТА">К</th>
                        <th class="mid-input cell" title="Оптимизация страницы">ОС</th>
                        <th class="mid-input cell" title="Подбор графики">ПГ</th>
                        <th class="mini-select cell" title="Колличество текста">КС</th>
                        <th class="mini-select cell" title="Цена за тысячу символов с пробелами">Ц</th>
                        <th class="input cell">Ключи</th>
                        <th class="mini-input cell" title="Минимальное количество ключей, которое должен ввести копирайтер">КК</th>
                        <th class="textarea cell">Комментарии</th> 
                        <th class="date cell" title="Время последнего обновления">Обновленно</th>
                    </tr>
                </thead>
                <tbody>    
                    {foreach $pages as $page}
                        {page_row page=$page type='pages' path=''}
                    {/foreach}
                    {foreach $categories as $cat}
                        {page_row page=$cat type='pages_categories' path=''}
                    {/foreach}
                    {foreach $posts as $post}
                        {page_row page=$post type='blog' path='blog/'}
                    {/foreach}
                    {foreach $catalogs as $catalog}
                        {page_row page=$catalog type='categories' path='catalog/'}
                    {/foreach}
                    {foreach $products as $product}
                        {page_row page=$product type='products' path='products/'}
                    {/foreach}
                </tbody>
            </table>
        </div>
            
        <div id="action">
            <label id="check_all" class="dash_link">Выбрать все</label>
            <input id="apply_action" class="button_green" type="submit" value="Применить">
        </div>
    </form>
</div>

{* On document load *}
{literal}
<script type="text/javascript" src="design/js/jquery.tablesorter/jquery.tablesorter.min.js"></script> 
<script>
$(function () {
    // Раскраска строк
    function colorize() {
        $(".row:even").addClass('even');
        $(".row:odd").removeClass('even');
    }

    // Раскрасить строки сразу
    colorize();

    // Выделить все
    $("#check_all").click(function () {
        $('#list input[type="checkbox"][name*="check"]').attr('checked', 1 - $('#list input[type="checkbox"][name*="check"]').attr('checked'));
    });
    
    $('.sel-with-other').change(function(){
        if($(this).val() == 'other'){
            $(this).next('input').show();
        }else{
            $(this).next('input').hide();
        }
    });
    
    $('.cb-for-input').change(function() {
        if($(this).is(":checked")) {
            $(this).next('input').show();
        }else{
            $(this).next('input').hide();
        }
    });
    
    $("#CR-table").tablesorter();
    
    $('#list_form').submit(function(e){
        var error = false;
        $('.cb-pages:checked').each(function(){
            var tr = $(this).closest('tr');
            if((tr.find('.keywords').val() != '' && !isNaN(tr.find('.keywords').val())) || isNaN(tr.find('.key_count').val()) || (tr.find('.sel-with-other:first').val() == 'other' && isNaN(tr.find('.sel-with-other:first').next('input').val())) || (tr.find('.sel-with-other:eq(1)').val() == 'other' && isNaN(tr.find('.sel-with-other:eq(1)').next('input').val()))){
                tr.removeClass('warning').removeClass('success').addClass('error');
                error = true;
            }else if(!tr.find('input.validate:checked').length){
                tr.removeClass('success').removeClass('error').addClass('warning');
                error = true;
            }else{
                tr.removeClass('warning').removeClass('error').addClass('success');
            }
        });
        if(error){
            e.preventDefault();
            var target = $("tr.error").length ? $("tr.error") : $("tr.warning");
            $('html, body').animate({
                scrollTop: target.offset().top
            }, 1300);
        }
    });
    
    $(window).scroll(function(){
      if($(document).scrollTop() > 274){
        $('.head-row').css({'position':'fixed'});
      }else{
        $('.head-row').css({'position':'static'});
      }
    });
});
</script>
{/literal}