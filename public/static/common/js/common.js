/**
 * Created by Sky9th on 2017/4/19.
 */

/**
 * 公共Javascript函数库目录
 * 0.ajax_submit | Ajax提交
 * 1.ajax_post | AjaxPost提交
 *  1.1.ajax_success | ajax_post提交后返回[status=1]时的默认回调
 *  1.2.ajax_fail | ajax_post提交后返回[status=0]时的默认回调
 *  1.3.ajax_error | ajax_post提交后返回异常时的默认回调
 * 2.captcha_refresh | 验证码刷新
 * 3.notification | 通知函数
 * 4.location_frame | 重新定位框架
 * 5.loading | 全屏加载层弹出
 * 6.loaded | 全屏加载层隐藏
 * 7.open_add_form | 加载数据增加表单
 * 8.open_edit_form | 加载数据修改表单
 * 9.ajax_deletes | ajax提交批量删除
 * 10.ajax_delete | ajax提交删除
 */

/**
 *
 * @param data 传入数据
 * @param action 提交地址
 * @param type 提交类型
 * @param captcha 需要刷新的验证码dom
 * @param success 成功回调
 * @param fail 失败回调
 * @param error 错误回调
 * @param load 加载层
 * @param complete 完成回调
 */
function ajax_submit(data, action, type, captcha, success, fail, error, load, complete){
    if( load === true ){
        load = function(){return true}
    }else{
        load = loading;
    }
    $.ajax({
        url : action,
        type : type ,
        data : data ,
        dataType : 'json',
        beforeSend : load,
        success : function(data){
            if( data.code == '1' ){
                if( typeof success != 'function' ){
                    success = ajax_success;
                }
                success(data);
            }else {
                if (data.code == '0') {
                    if (typeof fail != 'function') {
                        fail = ajax_fail;
                    }
                    fail(data);
                } else {
                    if (typeof error != 'function') {
                        error = ajax_error;
                    }
                    error(data);
                }
                if( typeof captcha == 'object' ){
                    for(var i = 0 ; i < captcha.length ; i++){
                        captcha_refresh(captcha[i])
                    }
                }
            }
        },
        complete : function(){
            if( typeof complete !== 'function' ){
                complete = loaded;
            }
            complete();
        },
        error: ajax_error
    })
}

/**
 * 1 AJAX 表单提交
 * @param form_id  表单ID
 * @param action  提交地址
 * @param success  成功回调
 * @param fail  失败回调
 * @param error  错误回调
 */
function ajax_form(form_id, action, type ,success, fail, error){
    if( !action ) {
        action = $('#' + form_id).attr('action')
    }
    if( type == 'delete' ){
        if( !confirm('是否要继续进行该操作') ){
            return false;
        }
    }
    var captcha = $('#' + form_id).find('.captcha_verify');
    var data = $('#' + form_id).serialize();
    ajax_submit(data, action, type, captcha, success, fail, error);
}

/**
 * 1.1 ajax_post提交后返回[status=1]时的默认回调
 * @param data
 */
function ajax_success(data){
    notification(data.code, data.msg, data.url);
}

/**
 * 1.2 ajax_post提交后返回[status=0]时的默认回调
 * @param data
 */
function ajax_fail(data){
    notification(data.code , data.msg);
}

/**
 * 1.3 ajax_post提交后返回异常时的默认回调
 * @param data
 */
function ajax_error(data){
    notification(0 , '系统错误，请稍后再试');
}

/**
 * 1.4 AJAX POST 提交
 * @param form_id  表单ID
 * @param action  提交地址
 * @param success  成功回调
 * @param fail  失败回调
 * @param error  错误回调
 */
function ajax_post(form_id, action, success, fail, error){
    ajax_form(form_id, action, 'post', success, fail, error)
}

/**
 * 1.5 AJAX PUT 提交
 * @param form_id  表单ID
 * @param action  提交地址
 * @param success  成功回调
 * @param fail  失败回调
 * @param error  错误回调
 */
function ajax_put(form_id, action, success, fail, error){
    ajax_form(form_id, action, 'put', success, fail, error)
}


/**
 * 1.6 AJAX delete 提交
 * @param form_id  表单ID
 * @param action  提交地址
 * @param success  成功回调
 * @param fail  失败回调
 * @param error  错误回调
 */
function ajax_delete(form_id, action, success, fail, error){
    ajax_form(form_id, action, 'delete', success, fail, error)
}

/**
 * 2.验证码刷新
 * @param obj
 */
function captcha_refresh(obj){
    var _src = $(obj).attr('_src') + '?t=' + Math.random();
    $(obj).attr('src',_src);
}

/**
 * 3.通知函数
 * @param status 状态
 * @param info 提示语
 * @param url 跳转地址
 * @param show 通知发起时的回调
 * @param close 通知结束时的回调
 * @param parent 通知出现位置的父节点
 */
function notification(status, info, url, show, close){
    var type = '';
    var timeout = 4000;
    switch(status){
        case 0:
            type = 'error';
            break;
        case 1:
            type = 'success';
            timeout = 2000;
            break;
        case 2:
            type = 'info';
            break;
        case 3:
            type = 'warning';
            break;
        default :
            type = 'default';
            break;
    }
    toastr.options = {
        "closeButton": true,
        "debug": false,
        "progressBar": false,
        "positionClass": "toast-top-right",
        "onclick": null,
        "showDuration": "300",
        "hideDuration": "1000",
        "timeOut": "3000",
        "extendedTimeOut": "1000",
        "showEasing": "swing",
        "hideEasing": "linear",
        "showMethod": "fadeIn",
        "hideMethod": "fadeOut"
    }
    toastr.options.onShown = function() {
        if( typeof show == 'function' ){
            show(status, info, url);
        }
    }
    toastr.options.onHidden = function() {
        if( typeof close == 'function' ){
            close(status, info, url);
        }else if( url) {
            if( url == 'self' ){
                window.location.reload()
            }else if( url == 'close' ){
                window.close()
                //当你在iframe页面关闭自身时
                var index = parent.layer.getFrameIndex(window.name); //先得到当前iframe层的索引
                if( index ){
                    parent.window.location.reload();
                }
                parent.layer.close(index); //再执行关闭
            }else{
                window.location.href = url
            }
        }
    }
    toastr[type](info);
}

/**
 * 4.重新定位框架
 * @param obj
 */
function location_frame(obj){
    $('#frame').attr('src', obj.href );
    $('.page-sidebar .sidebar-menu .menu-items a').removeClass('is-active')
    $('.page-sidebar .sidebar-menu .menu-items span').removeClass('bg-success')
    $(obj).addClass('is-active')
    $(obj).next().addClass('bg-success')
    return false;
}

/**
 * 5.全屏加载层弹出
 */
var fullLoading_setTimeout = '';
function loading(){
    var mt = ($(window).height()-125)/2+'px';
    var loading = '<div id="fullLoading" class="loading" style="height:'+$(window).height()+'px"><div class="loading-bg"></div>  <div class="loading-content" style="margin-top:'+mt+'"> <h5 class="text-left p-b-5"><span class="semi-bold">加载中</span> <small>请稍后...</small></h5> <div class="text-center"> <i class="fa fa-spinner fa-spin fa-4x fa-fw"></i> <span class="sr-only">Loading...</span> </div> <p class="text-right hinted-text p-t-10 p-r-10">Loading...</p> </div> </div>'
    $('body').append(loading);
    fullLoading_setTimeout = setTimeout(function(){
        $('.loading .text-center').html('系统繁忙，请稍后再试');
        setTimeout(function(){
            loaded()
        },1000)
    },20000)
}

/**
 * 6.全屏加载层移除
 */
function loaded(){
    $('body').children('.loading').remove()
    clearTimeout(fullLoading_setTimeout);
}

/**
 * 7.加载数据增加表单
 * @param type   类型
 * @param url
 */
function open_add_form(type, url){
    $('.modal-edit-form form').attr('action','')
    $('.modal-edit-form h5 em').text('添加')
    $('.modal-edit-form input[name=_method]').val('POST')
    if( type == 'top' ){
        $('#simpleAddForm').modal('show')
        $.ajax({
            url: url,
            type: 'get',
            success: function(data){
                $('#simpleAddForm .modal-body .form-body').html(data)
            }
        })
    }else if( type == 'right' ){
        $('#modalSlideLeft').modal('show')
        $.ajax({
            url: url,
            type: 'get',
            success: function(data){
                $('#modalSlideLeft .modal-body .form-body').html(data)
            }
        })
    }else if( type == 'page' ){
        window.location.href = url;
    }else if( type == 'open'){
        open_frame(url);
    }
}

/**
 * 8.加载数据修改表单
 * @param type
 * @param url
 * @param action
 */
function open_edit_form(type, url, action){
    open_add_form(type,url);
    $('.modal-edit-form form').attr('action',action)
    $('.modal-edit-form h5 em').text('修改')
    $('.modal-edit-form input[name=_method]').val('PUT')
}

/**
 * 9.ajax提交批量删除
 * @param form_id
 * @param action
 * @param success
 * @param fail
 * @param error
 */
function ajax_deletes(form_id, action, success, fail, error){
    if( !confirm('确定是否继续进行该操作？') ){
        return false;
    }
    if( !form_id ){
        form_id = 'form-work-table'
    }
    if( !action ){
        action = $('#'+form_id).attr('action')
    }
    var data = $('#'+form_id).serialize();
    ajax_submit(data, action, 'delete', success, fail, error)
}

/**
 * 10.ajax提交删除
 * @param obj
 * @param success
 * @param fail
 * @param error
 */
function ajax_delete(obj, success, fail, error){
    if( !confirm('确定是否继续进行该操作？') ){
        return false;
    }
    var action = $(obj).attr('href')
    ajax_submit('', action, 'delete', success, fail, error)
}

/**
 * 11.联动下拉列表
 * @param table
 * @param pid
 * @param url
 * @param obj
 * @param parent
 */
function linkage_select(table, pid, url, obj, parent) {
    var id = $(obj).val();
    var prefix = $('#'+parent).attr('id');
    if( obj == '' ){
        id = 0;
    }else{
        id = $(obj).val();
        if( !id ){
            return ;
        }
        var next = $(obj).parent();
        while( next.next().length ){
            next.next().remove();
        }
    }
    var _u = url.replace('__table__',table)
    _u = _u.replace('__id__',id);
    _u = _u.replace('__pid__',pid);
    $.ajax({
        url: _u,
        type: 'get',
        success:function(data){
            if( data.status == '1' ){
                var val = $(obj).val()
                $('#'+parent).next().val(val);
                if( data.info.length > 0 ){
                    var select = '<section><select class="full-width" onchange="linkage_select('+prefix+'_table, '+prefix+'_pid, '+prefix+'_url, this, '+prefix+'_parent)"></select></section>';
                    $('#'+parent).append(select);
                    var _d = data.info;
                    for (var i in _d) {
                        $('#'+parent).children().last().find('select').append('<option value="' + _d[i].id + '">' + _d[i].text + '</option>')
                    }
                    $('#'+parent).children().last().find('select').select2()
                }
            }
        }
    })
}


/**
 * 12.加载窗口
 * @param url 加载地址
 * @param id ID
 * @param data 带参数组
 * @param status 状态
 */
function open_modal(url, id, data, status){
    if( !id ){
        id = 'origin';
    }
    if( status ){
        status = 'modal-'+status;
    }
    $.ajax({
        url: url,
        data: data,
        type: 'get',
        success: function(data){
            var modal = '<div class="modal '+status+' fade slide-up disable-scroll" id="open_modal_'+id+'" tabindex="-1" role="dialog" aria-hidden="false"> <div class="modal-dialog "> <div class="modal-content-wrapper"> <div class="modal-content"></div> </div> </div> </div>'
            if( $('#open_modal_'+id).length < 1 ){
                $('body').append(modal);
            }
            $('#open_modal_'+id+' .modal-content').html(data);
            $('#open_modal_'+id).modal('show');
        }
    })
}


var current_file_selector ;
var current_file_selector_callback
/**
 * 12.1 加载图片管理窗口
 * @param obj
 * @param url
 * @param callback
 */
function open_file_modal(obj, url, callback) {
    current_file_selector = obj;
    current_file_selector_callback = callback;
    open_modal(url, 'image')
}

/**
 * 12.2 加载图标管理窗口
 * @param obj
 * @param url
 * @param callback
 */
function open_icon_frame(obj, url) {
    $('.icon-select').removeClass('current-icon-select')
    $(obj).parent().parent().parent().addClass('current-icon-select');
    layer.open({
        type: 2,
        shadeClose: true,
        shade: 0.8,
        title: false,
        area: ['90%', '90%'],
        content: url //iframe的url
    });
}

var current_wechat_media_selector ;
/**
 * 12.3 加载微信素材选择窗口
 * @param event
 * @param obj
 */
function open_wechat_media_selector(event, obj) {
    var x = event.pageX;
    var y = event.pageY;
    var window_width = $(window).width();
    var view_width = $('#wechat_media_selector').width();
    var css = new Object();
    if( window_width - x < view_width ){
        $.extend(css, { right:(window_width - x)+'px' } );
    }else{
        $.extend(css, { left: x+'px' } );
    }
    $.extend(css, { top:y+10+'px' } );
    $('#wechat_media_selector').css(css);
    $('#wechat_media_selector').show();
    $('#wechat_media_selector_bg').show();
    $('#wechat_media_selector_bg').click(function(){
        close_wechat_media_selector();
    })
    $('#wechat_media_selector_close').click(function(){
        close_wechat_media_selector();
    })
    current_wechat_media_selector = obj;

}

/**
 * 12.4 关闭微信素材选择窗口
 */
function close_wechat_media_selector() {
    $('#wechat_media_selector').hide();
    $('#wechat_media_selector_bg').hide();
}

/**
 * 13 加载iframe窗口
 * @param url
 */
function open_frame(url,width,height) {
    if(!width){
        width = "90%"
    }
    if(!height){
        height = "90%"
    }
    layer.open({
        type: 2,
        shadeClose: true,
        title: false,
        area: [ width, height ],
        content: url //iframe的url
    });
}

function get_files_preview(id) {

}

/**
 * js初始化
 */
function init() {
    //图片上传，删除按钮
    $(document).on('click','.image-item',(function(){
        $('.image-item-edit').css('display','none')
        $(this).find('.image-item-edit').fadeToggle();
    }))
}
init();
