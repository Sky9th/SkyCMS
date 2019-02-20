<?php
/**
 * Created by PhpStorm.
 * User: Sky9th
 * Date: 2019/1/7
 * Time: 9:50
 */

return [

    'status_badge'=>  [
        -1 => '<span class="badge badge-default">删除</span>',
        0  => '<span class="badge badge-danger">禁用</span>',
        1  => '<span class="badge badge-success">正常</span>'
    ],

    'status_name' => [
        -1 => '删除',
        0  => '禁用',
        1  => '启用',
    ],

    'action_type' => [
        '0' => '普通路由',
        '1' => '资源路由',
        '2' => '行为规则',
    ],

    'ueditor' => "[
            'fullscreen', 'source', '|', 'undo', 'redo', '|',
            'bold', 'italic', 'underline', 'fontborder', 'strikethrough',  'removeformat', 'formatmatch', 'autotypeset', 'blockquote', 'pasteplain', '|', 'forecolor', 'backcolor', 'insertorderedlist', 'insertunorderedlist', 'selectall', 'cleardoc', '|',
            'rowspacingtop', 'rowspacingbottom', 'lineheight', '|',
            'fontfamily', 'fontsize', '|',
            'directionalityltr', 'directionalityrtl', 'indent', '|',
            'justifyleft', 'justifycenter', 'justifyright', 'justifyjustify', '|', 'touppercase', 'tolowercase', '|',
            'link', 'unlink', 'anchor', '|', 'imagenone', 'imageleft', 'imageright', 'imagecenter', '|',
            'simpleupload', 'insertimage', 'attachment', 'map',  'insertcode',  'pagebreak', 'template', 'background', '|',
            'horizontal', 'date', 'time', 'spechars', 'snapscreen', 'wordimage', '|',
            'inserttable', 'deletetable', 'insertparagraphbeforetable', 'insertrow', 'deleterow', 'insertcol', 'deletecol', 'mergecells', 'mergeright', 'mergedown', 'splittocells', 'splittorows', 'splittocols', 'charts', '|',
            'print', 'searchreplace', 'drafts'
        ]",

    'linkage' => [
        'area',
        'article_category',
        'role',
        'subject'
    ],

    'extension' => [
        'file' => 'doc,docx,xls,xlsx,ppt,pdf,zip,rar,7z,jpg,png,gif,sql,xml,rss,mp4,flv,avi,mp3,amr,txt',
        'image' => 'jpg,png,gif',
        'video' => 'mp4,flv,avi',
        'audio' => 'avi,mp3,amr',
        'document' => 'doc,docx,xls,xlsx,ppt,pdf,zip,rar,7z,sql,xml,rss'
    ]
];