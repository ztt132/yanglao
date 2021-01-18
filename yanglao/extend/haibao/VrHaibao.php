<?php
/**
 * Created by PhpStorm.
 * User: cesc
 * Date: 2020/9/14
 * Time: 2:58 PM
 */

class VrHaibao
{
    protected $im;
    protected $imWidth = 750;
    protected $imHeight = 1334;

    protected $borderTop = 134;
    protected $orgPicWidth = 552;

    protected $titleLeft = 25;
    protected $titleRight = 25;
    protected $titleTop = 754;

    protected $titleSpace = 10;

    protected $titleFont = './static/haibao/font/Microsoft Yahei.ttf';
    protected $mediumFont = './static/haibao/font/Microsoft Yahei.ttf';

    CONST BASE_UPLOAD_DIR = '/static/haibao/haibao';

    public function make($name,$bedNumber,$types,$qrcodeImage,$pic = '')
    {
        // 创建画布
        $this->createImg();
        // 设置背景
        $this->setBackGround();
        // 放置机构图片
        $this->setOrgPic($pic);
        // 放入机构名称
        $this->setOrgName($name);
        // 设置床位
        $this->setBedNumber($bedNumber);
        // 设置类型
        $this->setType($types);
        // 设置二维码
        $this->setQrcode($qrcodeImage);

        // 创建目录
        $dir = make_file_dir(self::BASE_UPLOAD_DIR);
        $fileName = make_file_name('jpg');
        imagejpeg($this->im,$dir.'/'.$fileName);
        imagedestroy($this->im);

        $haibaoImage = get_protocol().'://'.get_server_name().SELF::BASE_UPLOAD_DIR . '/' . date('Ymd',time()) . '/' . $fileName;
        return $haibaoImage;
    }

    protected function createImg()
    {
        $this->im = @imagecreatetruecolor($this->imWidth, $this->imHeight);
        $backgroundColor = imagecolorallocate($this->im, 245, 245, 245);
        imagefill($this->im,0,0,$backgroundColor);
    }

    protected function setBackGround()
    {
        $background = imagecreatefrompng('./static/haibao/images/background.png');
        imagecopy($this->im,$background,0,0,0,0,$this->imWidth,$this->imHeight);
        imagedestroy($background);
    }

    /**
     *
     */
    protected function setOrgPic($pic = '')
    {
        if (empty($pic)) {
            $orgPic = imagecreatefrompng('./static/haibao/images/org_default.png');
        } else {
            $orgPic = imagecreatefromjpeg($pic);
        }

        $border = imagecreatefrompng('./static/haibao/images/border.png');

        $borderDstX = ($this->imWidth - imagesx($border))/2;
        $orgDstX = ($this->imWidth-$this->orgPicWidth)/2;

        $srcX = (imagesx($orgPic)-$this->orgPicWidth)/2;
        imagecopy($this->im,$orgPic,$orgDstX,$this->borderTop+3,$srcX,0,$this->orgPicWidth,380);
        //
        imagecopy($this->im,$border,$borderDstX,$this->borderTop,0,0,imagesx($border),imagesy($border));
        imagedestroy($border);
        imagedestroy($orgPic);
    }

    protected function setOrgName($name = "")
    {
        $left = imagecreatefrompng('./static/haibao/images/left.png');
        $right = imagecreatefrompng('./static/haibao/images/right.png');
        $middle = imagecreatefrompng('./static/haibao/images/middle.png');

        imagecopy($this->im,$left,$this->titleLeft,$this->titleTop,0,0,imagesx($left),imagesy($left));
        imagecopy($this->im,$right,($this->imWidth-$this->titleRight-imagesx($right)),$this->titleTop,0,0,imagesx($right),imagesy($right));
        $middleSrcX = $this->imWidth-imagesx($left)-imagesx($right)-$this->titleLeft-$this->titleRight;
        imagecopyresized($this->im,$middle,$this->titleLeft+imagesx($left),$this->titleTop,0,0,$middleSrcX,imagesy($middle),imagesx($middle),imagesy($middle));

        // 获取文字宽度 每个字之间间距10px
        $titleWidth = $this->getWordLen($name,30,$this->titleFont);
        $length = mb_strlen($name);
        $width = $titleWidth + ($length - 1) * $this->titleSpace;
        $titleColor = imagecolorallocate($this->im,255,255,255);
        $start = ($this->imWidth-$width)/2;//初始文字位置
        for ($i = 0;$i<$length;$i++) {
            $word = mb_substr($name,$i,1);
            imagettftext($this->im,30,0,$start,810,$titleColor,$this->titleFont,$word);
            imagettftext($this->im,30,0,$start+1,810,$titleColor,$this->titleFont,$word);
            // 下次文字的开始位置 +10 加当前文字的长度
            $tempW = $this->getWordLen($word,28,$this->titleFont);
            $start += 10;
            $start += $tempW;
        }

        imagedestroy($left);
        imagedestroy($right);
        imagedestroy($middle);
    }

    protected function setBedNumber($bedNumber = 0)
    {
        $word = "床位数: ".$bedNumber;
        $wordColor = imagecolorallocate($this->im,255,255,255);
        $width = $this->getWordLen($word,28,$this->titleFont);
        $x = ($this->imWidth-$width)/2;
        imagettftext($this->im,28,0,$x,903,$wordColor,$this->titleFont,$word);
    }

    protected function setType($types = [])
    {
        // 第一个框开始的位置
        $start = 50;
        $space = 70;//间隔
        $width = 170;//每个方框的宽度
        $height = 52;//每个方框的高度

        $typeBack = @imagecreatetruecolor($width, $height);
        $backgroundColor = imagecolorallocate($this->im, 183, 190, 206);
        imagefill($typeBack,0,0,$backgroundColor);

        $wordColor = imagecolorallocate($this->im,6,16,43);
        foreach ($types as $v) {
            if (empty($v)) {
                continue;
            }
            imagecopy($this->im,$typeBack,$start,974,0,0,$width,$height);
            // 放入文字
            $wordLength = $this->getWordLen($v,26,$this->mediumFont);
            $wordStart = $start + ($width-$wordLength)/2;
            imagettftext($this->im,26,0,$wordStart,1014,$wordColor,$this->mediumFont,$v);
            $start = $start + $space + $width;
        }
        imagedestroy($typeBack);
    }

    protected function setQrcode($qrcodeImage)
    {
        $back = imagecreatefrompng('./static/haibao/images/qrcodeBack.png');
        $qrcode = imagecreatefromjpeg($qrcodeImage);

        $backX = (750-imagesx($back))/2;
        $qrcodeX = (750-178)/2;
        imagecopyresized($this->im,$qrcode,$qrcodeX,1070,0,0,178,178,imagesx($qrcode),imagesy($qrcode));
        imagecopy($this->im,$back,$backX,1060,0,0,imagesx($back),imagesy($back));


        imagedestroy($back);
    }

    private function getWordLen($str,$fontSize,$font)
    {
        $box = imagettfbbox($fontSize,0,$font,$str);
        $width = $box[2] - $box[0];
//        $height = abs($box[7] - $box[1]);
        return $width;
    }
}