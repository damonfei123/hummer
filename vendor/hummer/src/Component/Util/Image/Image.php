<?php
/*************************************************************************************

   +-----------------------------------------------------------------------------+
   | Hummer [ Make Code Beauty And Web Easy ]                                    |
   +-----------------------------------------------------------------------------+
   | Copyright (c) 2014 https://github.com/damonfei123 All rights reserved.      |
   +-----------------------------------------------------------------------------+
   | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )                     |
   +-----------------------------------------------------------------------------+
   | Author: Damon <zhangyinfei313com@163.com>                                   |
   +-----------------------------------------------------------------------------+

**************************************************************************************/
namespace Hummer\Component\Util\Image;

/*
 * 已知问题：1.在图片缩放功能中，使用imagecreatetruecolor函数创建画布，并使用透明处理算法，
 * 但PNG格式的图片无法透明。用imagecreate函数创建画布可以解决这个问题，但是缩放出来的图片色数太少了
 *
 *  type值：
 *  (1)：代表使用图片缩放功能，此时，$value1代表缩放后图片的宽度，$value2代表缩放后图片的高度
 *  (2):代表使用图片裁剪功能，此时，$value1代表裁剪开始点的坐标，例：从原点开始即是“0,0”前面是x轴后面是y轴，中间用,分隔，$value2代表裁剪的宽度和高度，同样也是“20，20”的形式使用
 *  (3):代表使用加图片水印功能，此时，$value1代表水印图片的文件名，
 *   $value2代表水印在图片中的位置，有10值个可以选,1代表左上，2代表左中，3代表左右，
 *   4代表中左，5代表中中，6代表中右，7代表下做，8代表下中，9代表下右，0代表随机位置
 *
 *
 *  Usage:
 *  $image = new image("2.png", 1, "300", "500", "5.png"); //使用图片缩放功能
 *  $image = new image("2.png", 2, "0,0", "50,50", "5.png"); //使用图片裁剪功能
 *  $image = new image("2.png", 3, "1.png", "0", "5.png"); //使用加图片水印功能
 *  $image->outimage();
 */

class Image{

    /**
     *  @var $types  使用的功能编号，1为图片缩放功能 2为图片裁剪功能 3,为图片加图片水印功能
     **/
    private $types;

    /**
     *  @var $imagetype image type
     **/
    private $imgtype;

    /**
     * @var $image image resource
     **/
    private $image;

    /**
     * @var $width image width
     **/
    private $width;//图片宽度

    /**
     * @var height  image height
     **/
    private $height;

    /**
     * @var $value1 type
     **/
    private $value1;

    /**
     * @var $value2 type
     **/
    private $value2;

    /**
     *  @var $endaddress output address + filename
     **/
    private $endaddress;

    function __construct(
        $imageaddress,
        $types,
        $value1='',
        $value2='',
        $endaddress
    ){
        $this->types      = $types;
        $this->image      = $this->imagesources($imageaddress);
        $this->width      = $this->imagesizex();
        $this->height     = $this->imagesizey();
        $this->value1     = $value1;
        $this->value2     = $value2;
        $this->endaddress = $endaddress;
    }
    //根据传入type值的不同，输出不同的功能
    public function outimage(){
        switch($this->types){
            case 1:
            $this->scaling();
            break;
        case 2:
            $this->clipping();
            break;
        case 3:
            $this->imagewater();
            break;
        default:
            return false;
        }
    }

    //加图片水印功能
    private function imagewater(){
        //用函数获取水印文件的长和宽
        $imagearrs=$this->getimagearr($this->value1);
        //调用函数计算出水印加载的位置
        $positionarr=$this->position($this->value2, $imagearrs[0], $imagearrs[1]);
        //加水印
        imagecopy(
            $this->image,
            $this->imagesources($this->value1),
            $positionarr[0],
            $positionarr[1],
            0,
            0,
            $imagearrs[0],
            $imagearrs[1]
        );
        //调用输出方法保存
        $this->output($this->image);
    }
    private function clipping(){ //图片裁剪功能
        //将传进来的值分别赋给变量
        list($src_x, $src_y)=explode(",", $this->value1);
        list($dst_w, $dst_h)=explode(",", $this->value2);
        //这个判断就是限制不能截取到图片外面去
        if($this->width < $src_x+$dst_w || $this->height < $src_y+$dst_h){
            return false;
        }
        //创建新的画布资源
        $newimg=imagecreatetruecolor($dst_w, $dst_h);
        //进行裁剪
        imagecopyresampled(
            $newimg,
            $this->image,
            0,
            0,
            $src_x,
            $src_y,
            $dst_w,
            $dst_h,
            $dst_w,
            $dst_h
        );
        //调用输出方法保存
        $this->output($newimg);
    }
    private function scaling(){ //图片缩放功能
        //获取等比缩放的宽和高
        $this->proimagesize();
        //根据参数进行缩放,并调用输出函数保存处理后的文件
        $this->output($this->imagescaling());
    }
    private function imagesources($imgad){ //获取图片类型并打开图像资源
        $imagearray=$this->getimagearr($imgad);
        switch($imagearray[2]){
            case 1://gif
                $this->imgtype=1;
                $img=imagecreatefromgif($imgad);
                break;
            case 2://jpeg
                $this->imgtype=2;
                $img=imagecreatefromjpeg($imgad);
                break;
            case 3://png
                $this->imgtype=3;
                $img=imagecreatefrompng($imgad);
                break;
            default:
                return false;
        }
        return $img;
    }
    private function imagesizex(){ //获得图片宽度
        return imagesx($this->image);
    }
    private function imagesizey(){ //获取图片高度
        return imagesy($this->image);
    }
    private function proimagesize(){ //计算等比缩放的图片的宽和高
        if($this->value1 && ($this->width < $this->height)) { //等比缩放算法
            $this->value1=round(($this->value2 / $this->height) * $this->width);
        }else{
            $this->value2=round(($this->value1/ $this->width) * $this->height);
        }
    }
    private function imagescaling(){//图像缩放功能，返回处理后的图像资源
        $newimg = imagecreatetruecolor($this->value1, $this->value2);
        $tran   = imagecolortransparent($this->image);//处理透明算法
        if($tran >= 0 && $tran < imagecolorstotal($this->image)){
            $tranarr  = imagecolorsforindex($this->image, $tran);
            $newcolor = imagecolorallocate(
                $newimg,
                $tranarr['red'],
                $tranarr['green'],
                $tranarr['blue']
            );
            imagefill($newimg, 0, 0, $newcolor);
            imagecolortransparent($newimg, $newcolor);
        }
        imagecopyresampled(
            $newimg,
            $this->image,
            0,
            0,
            0,
            0,
            $this->value1,
            $this->value2,
            $this->width,
            $this->height
        );
        return $newimg;
    }
    private function output($image){//输出图像
        switch($this->imgtype){
            case 1:
            imagegif($image, $this->endaddress);
            break;
        case 2:
            imagejpeg($image, $this->endaddress);
            break;
        case 3:
            imagepng($image, $this->endaddress);
            break;
        default:
            return false;
        }
    }
    private function getimagearr($imagesou){//返回图像属性数组方法
        return getimagesize($imagesou);
    }
    //根据传入的数字返回一个位置的坐标,$width和$height分别代表插入图像的宽和高
    private function position($num, $width, $height){
        switch($num){
        case 1:
            $positionarr[0]=0;
            $positionarr[1]=0;
            break;
        case 2:
            $positionarr[0]=($this->width-$width)/2;
            $positionarr[1]=0;
            break;
        case 3:
            $positionarr[0]=$this->width-$width;
            $positionarr[1]=0;
            break;
        case 4:
            $positionarr[0]=0;
            $positionarr[1]=($this->height-$height)/2;
            break;
        case 5:
            $positionarr[0]=($this->width-$width)/2;
            $positionarr[1]=($this->height-$height)/2;
            break;
        case 6:
            $positionarr[0]=$this->width-$width;
            $positionarr[1]=($this->height-$height)/2;
            break;
        case 7:
            $positionarr[0]=0;
            $positionarr[1]=$this->height-$height;
            break;
        case 8:
            $positionarr[0]=($this->width-$width)/2;
            $positionarr[1]=$this->height-$height;
            break;
        case 9:
            $positionarr[0]=$this->width-$width;
            $positionarr[1]=$this->height-$height;
            break;
        case 0:
            $positionarr[0]=rand(0, $this->width-$width);
            $positionarr[1]=rand(0, $this->height-$height);
            break;
        }
        return $positionarr;
    }
    function __destruct(){
        imagedestroy($this->image);
    }
}
?>
