/**
 * Created by Sky9th on 2017/4/19.
 */

/**
 * Javascript初始化
 * 1.init_date | AjaxPost提交
 */


$(function(){

    $('.search-advance-btn').click(function(e){
        e.stopPropagation();
        $('.search-advance').toggleClass('hide');
        $('body').prepend('<div class="search-close-bg"></div>')
        $('.search-close-bg').css('height',$(window).height()+'px')
    })

    $(document).on('click','.search-close-bg',(function(){
        $('.search-advance').toggleClass('hide')
        $(this).remove();
    }))

    $('.checkbox .checkbox-all').click(function(){
        var name = $(this).attr('data-name');
        var checkbox = $('input[name="'+name+'"]')
        $.each(checkbox,function(){
            $(this).prop('checked') ? $(this).prop('checked',false) : $(this).prop('checked',true)
        })
    })

    $.each($('.form-group .control-label'),function(){
        $(this).text( $(this).next().children().eq(0).attr('title')  );
    })

    moment.locale('zh-cn');
    $('.js-date-range').daterangepicker({
        autoUpdateInput: false,
        locale : {
            direction: 'ltr',
            format: moment.localeData().longDateFormat('L'),
            separator: ' - ',
            applyLabel: '确定',
            cancelLabel: '取消',
            weekLabel: 'W',
            customRangeLabel: '自定义',
            daysOfWeek: moment.weekdaysMin(),
            monthNames: moment.monthsShort(),
            firstDay: moment.localeData().firstDayOfWeek()
        },
        ranges: {
            '今天': [moment(), moment()],
            '最近7天': [moment().subtract(6, 'days'), moment()],
            '本月': [moment().startOf('month'), moment().endOf('month')],
            '本年': [moment().startOf('year'), moment().endOf('year')]
        }
    });
    $('.js-date-range').on('apply.daterangepicker', function(ev, picker) {
        $(this).val(picker.startDate.format('YYYY-MM-DD') + ' - ' + picker.endDate.format('YYYY-MM-DD'));
        $(this).next().val(picker.startDate.format('YYYY-MM-DD'))
        $(this).next().next().val(picker.endDate.format('YYYY-MM-DD'))
    });

    $('.js-date-range').on('cancel.daterangepicker', function(ev, picker) {
        $(this).val('');
    });

    var flatpickr_zh =  {
        weekdays: {
            shorthand: ["周日", "周一", "周二", "周三", "周四", "周五", "周六"],
            longhand: [
                "星期日",
                "星期一",
                "星期二",
                "星期三",
                "星期四",
                "星期五",
                "星期六",
            ],
        },

        months: {
            shorthand: [
                "一月",
                "二月",
                "三月",
                "四月",
                "五月",
                "六月",
                "七月",
                "八月",
                "九月",
                "十月",
                "十一月",
                "十二月",
            ],
            longhand: [
                "一月",
                "二月",
                "三月",
                "四月",
                "五月",
                "六月",
                "七月",
                "八月",
                "九月",
                "十月",
                "十一月",
                "十二月",
            ],
        },

        rangeSeparator: " 至 ",
        weekAbbreviation: "周",
        scrollTitle: "滚动切换",
        toggleTitle: "点击切换 12/24 小时时制",
    }

    $('.time').flatpickr({
        locale: flatpickr_zh,
        enableTime: true
    });

    $('.date').flatpickr({
        locale: flatpickr_zh,
    });

})

