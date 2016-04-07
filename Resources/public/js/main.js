/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

$(document).ready(function(){

    $('#addSettingProfiles').multiselect();
    $('#duplicateSettingProfiles').multiselect();

    $('.boolean').on('click', function(){
        // ----- implement ajax request to change the setting
        if(!$(this).hasClass('btn-primary')) {
            $(this).toggleClass('btn-primary');
            $(this).siblings().toggleClass('btn-primary');
        }
    });

    $('.setting-remove').on('click', function(event){
        event.preventDefault();
        var url = $(this).attr('url');
        $this = $(this);
        $.ajax({
            url: url,
            type: "DELETE",
            success: function () {
                $this.closest('tr').remove();
            }
        });
    });

    $('.setting-type').on('click', function(){
        if(!$(this).hasClass('btn-primary')) {
            $(this).toggleClass('btn-primary');
            $(this).siblings().removeClass('btn-primary');
            var $relElement = $('.setting-'+$(this).attr('rel')+'-div');
            $relElement.toggleClass('hidden');
            $relElement.siblings().addClass('hidden');
            $('#addSettingTypeInput').val($(this).attr('rel'));
        }
    });

    $('.setting-type-boolean').on('click', function(){
        if(!$(this).hasClass('btn-primary')) {
            $(this).toggleClass('btn-primary');
            $(this).siblings('label').removeClass('btn-primary');
            $(this).siblings('input').val($(this).attr('rel'));
        }
    });

    $('.settings-array-add').on('click', function(event){
        event.preventDefault();
        var key = parseInt($('#addSettingArray').attr('rel'))+1;
        $('#addSettingArray').attr('rel', key);
        var render = '<li>'
            +'<div class="input-group">'
            +'<input type="text" class="form-control" name="setting-array_'+key+'">'
            +'<span class="input-group-btn">'
            +'<button class="btn btn-danger" type="button" onclick="addArrayRemoveInput(this)"><i class="glyphicon glyphicon-remove"></i></button>'
            +'</span>'
            +'</div>'
            +'</li>';
        $('#addSettingArray').append(render);
    });
});

function addArrayRemoveInput(el){
    jQuery(el).parent().parent().parent().empty();
}

function arrayHandleList(el, event, data){
    event.preventDefault();
    var $el = jQuery(el);
    var render = '';
    var settings = jQuery.parseJSON(data);
    if($el.html() == 'more'){
        settings.forEach(function(val){
            render = render + '<li>' + val + '</li>';
        });
        data = data.replace(/(["])/g, '&#34 ');
        render = render + '<li><a href="#" onclick="arrayHandleList(this, event, \''+data+'\')">less</a></li>';
        $el.parent().parent().html(render);
    }else if($el.html() == 'less'){
        render = render + '<li>' + settings[0] + '</li>';
        render = render + '<li>' + settings[1] + '</li>';
        render = render + '<li>' + settings[2] + '</li>';
        data = data.replace(/(["])/g, '&#34 ');
        render = render + '<li><a href="#" onclick="arrayHandleList(this, event, \''+data+'\')">more</a></li>';
        $el.parent().parent().html(render);
    }
}
