<?php
/**
 * Created by PhpStorm.
 * User: Sky9th
 * Date: 2017/5/12
 * Time: 17:25
 */

namespace app\admin\controller;

use app\common\model\common\File;
use think\Controller;
use think\Db;

class Upload extends Controller{

    /**
     * TODO 文件管理器
     * @return array|mixed
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    public function Manager(){
        $file = new File();
        if(request()->isPost()){
            $map = [];
            $condition[] = ['title','like','%'.input('keyword').'%'];
            if( input('post.extension') ){
                $map['ext'] = explode(',',config('static.extension')[input('post.extension')].',folder');
            }else{
                if( input('post.type') == '0' ){
                    $map['ext'] = explode(',',config('static.extension')['image'].',folder');
                }else{
                    $map['ext'] = explode(',',config('static.extension')['file'].',folder');
                }
            }
            if( input('post.wechat') ){
                $condition[] = ['media_id', 'neq', ''];
            }
            $map['user_id'] = is_login();
            $map['pid'] = input('pid' , 0);
            $map['status'] = 1;
            $list = $file->where($map)->where($condition)->order('folder desc,create_time desc')->paginate(16, true, [ 'path'=>'' ]);
            return ['info'=>$list,'page'=>$list->render()];
        }
        if(request()->isPut()){
            $data = [
                'user_id' => is_login(),
                'pid' => input('pid',0),
                'title' => input('folder'),
                'src' => '',
                'media_id' => 'folder',
                'ext' => 'folder',
                'folder' => '1',
                'create_time' => time(),
            ];
            if( !$data['title'] ){
                return error('请填写名称');
            }
            $res = $file->insert($data);
            if( $res ){
                app_log(1, 0, 'image_folder_create' , $data);
                return success(lang('success'));
            }else{
                return error(lang('fail'));
            }
        }
        if(request()->isDelete()){
            $delete = input('delete.');
            $ids = $delete['ids'];
            $children = $file->where(['pid' => ['in',$ids]])->count();
            if( $children > 0 ){
                return error('子文件夹存在内容，无法删除');
            }else{
                $app_log = app_log(1,  $ids, 'file_manager_delete', 'files', '', true);
                $res = $file->where(['id' => ['in',$ids]])->delete();
                if( $res ){
                    $app_log->save();
                    return success(lang('success'));
                }else{
                    return error(lang('fail'));
                }
            }
        }
        return $this->fetch('admin@upload/manager',['type'=>input('extension')]);
    }

    /**
     * TODO 文件上传
     * @return false|string|\think\response\Json|\think\response\View
     */
    public function file(){
        if(request()->isPost()){
            $pid = input('pid',0);
            $wechat = input('wechat');
            $file = request()->file('file');
            config('app_trace', false);
            $tmp = env('RUNTIME_PATH') . 'file' . DIRECTORY_SEPARATOR;
            if( input('chunks') ) {
                $chunk = input('chunk');
                $name = input('name');
                $dir = str_replace('-', '0', input('hash'));
                // 移动到框架应用根目录/public/uploads/ 目录下
                $info = $file->move($tmp.$dir, $name.'.'.$chunk, true);
                if ($info) {
                    return json(success('上传成功'));
                } else {
                    return json(error($file->getError()));
                }
            }else if( input('combine') ){
                $path = env('ROOT_PATH') . 'public' . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'files';
                $dir = str_replace('-', '0', input('hash'));
                $chunks = $this->getFile($tmp.$dir);
                $old_name = $tmp.$dir.DIRECTORY_SEPARATOR.input('name');
                $explode = explode('.',input('name'));
                $ext = end($explode);
                foreach ($chunks as $key => $value) {
                    $size = file_put_contents($old_name, file_get_contents($tmp.$dir.DIRECTORY_SEPARATOR.$value), FILE_APPEND);
                    if( !$size ){
                        if( is_file($old_name) ){
                            unlink($old_name);
                        }
                        return json(error('文件合并失败'));
                    }
                }
                $new_file = uniqid().'.'.$ext;
                $new_src = $path.DIRECTORY_SEPARATOR;
                $dir = date('Ymd').DIRECTORY_SEPARATOR;
                if( !is_dir($new_src.$dir) ){
                    mkdir($new_src);
                }
                $res = rename($old_name, $new_src.$dir.$new_file);
                if( $res ){
                    return $this->insertFile(input('name'), $pid, $dir.$new_file, $ext, $wechat);
                }else{
                    if( is_file($old_name) ){
                        unlink($old_name);
                    }
                    return json(error('上传失败'));
                }
            }else{
                $_n = explode('.',$file->getInfo()['name']);
                if( in_array( end($_n) ,['gif','jpg','png']) ){
                    $path = env('ROOT_PATH') . 'public' . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'images';
                }else{
                    $path = env('ROOT_PATH') . 'public' . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'files';
                }
                $info = $file->move($path);
                if($info) {
                    $base = $file->getInfo();
                    return json($this->insertFile($base['name'], $pid, $info->getSaveName(), $info->getExtension(), $wechat));
                }else{
                    // 上传失败获取错误信息
                    return json_encode(error( $file->getError()));
                }
            }
        }
        return view();
    }

    /**
     * 获取文件列表
     * @param $dir
     * @return array
     */
    public function getFile($dir) {
        $fileArray[]=NULL;
        if (false != ($handle = opendir ( $dir ))) {
            $i=0;
            while ( false !== ($file = readdir ( $handle )) ) {
                //去掉"“.”、“..”以及带“.xxx”后缀的文件
                if ($file != "." && $file != ".."&&strpos($file,".")) {
                    $fileArray[$i]= $file;
                    if($i==100){
                        break;
                    }
                    $i++;
                }
            }
            //关闭句柄
            closedir ( $handle );
        }
        return $fileArray;
    }

    /**
     * 上传图片
     * @return \think\response\Json|\think\response\View
     */
    public function image(){
        $path = env('ROOT_PATH') . 'public' . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'images';
        if(request()->isPost()){
            $pid = input('pid');
            $wechat = input('wechat');
            $file = request()->file('file');
            // 移动到框架应用根目录/public/uploads/ 目录下
            $info = $file->validate(['ext'=>'jpg,png,gif'])->move($path);
            if($info){
                $base = $file->getInfo();
                return json($this->insertFile($base['name'],$pid, $info->getSaveName(), $info->getExtension(), $wechat));
            }else{
                // 上传失败获取错误信息
                return json(error( $file->getError()));
            }
        }
        return view('', ['path'=>$path]);
    }

    public function insertFile($title, $pid, $src, $ext, $wechat, $user_id = 0){
        if( !$user_id ){
            $user_id = is_login();
        }
        $data = [
            'user_id' => $user_id,
            'pid' => $pid,
            'title' => $title,
            'src' => $src,
            'ext' => $ext,
            'create_time' => time(),
        ];
        $file = new File();
        $res = $file->insert($data);
        $last_id = $file->getLastInsID();
        if( $res ){
            app_log(1, $last_id, 'image_upload' , $data);
            if( $wechat == '1' && class_exists('app\wechat\controller\Manager') ) {
                $wechat_manager = new Manager();
                if( in_array( $ext , explode(',',config('static.extension')['image'])) ){
                    $to_wechat = $wechat_manager->image($data['src']);
                }else if( in_array( $ext , explode(',',config('static.extension')['audio'] )) ){
                    $to_wechat = $wechat_manager->audio($data['src']);
                }else if( in_array( $ext , explode(',',config('static.extension')['video'] )) ){
                    $description = Db::table('files')->where('id',  $last_id)->value('description');
                    $to_wechat = $wechat_manager->video($data['src'], $title, $description);
                }else{
                    $to_wechat['status'] = 0 ;
                }
                if( $to_wechat['status'] == '1' ){
                    $media_id = $to_wechat['media_id'];
                    $result = Db::table('files')->where('id',$last_id)->update(['media_id'=>$media_id]);
                    if( !$result ){
                        return error('图片上传至微信服务器失败');
                    }
                }
            }
            return success('上传成功','',$last_id);
        }else{
            return error('上传错误');
        }
    }

}