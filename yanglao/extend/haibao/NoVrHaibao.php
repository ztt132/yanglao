<?php
/**
 * Created by PhpStorm.
 * User: cesc
 * Date: 2020/9/14
 * Time: 2:58 PM
 */

class NoVrHaibao
{
    protected $im;
    protected $imWidth = 750;
    protected $imHeight = 1334;

    protected $titleFont = './static/haibao/font/Microsoft Yahei.ttf';
    protected $mediumFont = './static/haibao/font/Microsoft Yahei.ttf';

    CONST BASE_UPLOAD_DIR = '/static/haibao/haibao';

    public function make($name,$bedNumber,$types,$qrcodeImage,$pic = '')
    {
        $this->createImg();
        // 设置背景
        $this->setBackGround();

        // 机构名称
        $this->setOrgName($name);
        // 床位
        $this->setBedNumber($bedNumber);

        $this->setTypes($types);

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
        $background = imagecreatefrompng('./static/haibao/novrimages/background.png');
        imagecopy($this->im,$background,0,0,0,0,$this->imWidth,$this->imHeight);
        imagedestroy($background);
    }

    protected function setOrgName($name = "")
    {
        $wordColor = imagecolorallocate($this->im,255,255,255);
        $length = $this->getWordLen($name,30,$this->titleFont);
        $start = (750-$length)/2;
        imagettftext($this->im,30,0,$start,380,$wordColor,$this->titleFont,$name);
        imagettftext($this->im,30,0,$start+1,380,$wordColor,$this->titleFont,$name);
    }

    protected function setBedNumber($bedNumber = 0)
    {
        $word = "床位数: ".$bedNumber;
        $wordColor = imagecolorallocate($this->im,255,255,255);
        $width = $this->getWordLen($word,28,$this->titleFont);
        $x = ($this->imWidth-$width)/2;
        imagettftext($this->im,28,0,$x,905,$wordColor,$this->titleFont,$word);
    }

    protected function setTypes($types = [])
    {
        // 第一个框开始的位置
        $start = 50;
        $space = 70;//间隔
        $width = 172;//每个方框的宽度
        $height = 52;//每个方框的高度

        $typeBack = imagecreatefrompng('./static/haibao/novrimages/tagBack.png');
        $wordColor = imagecolorallocate($this->im,255,115,0);
        foreach ($types as $v) {
            if (empty($v)) {
                continue;
            }

            imagecopy($this->im,$typeBack,$start,974,0,0,$width,$height);
            // 放入文字
            $wordLength = $this->getWordLen($v,26,$this->titleFont);
            $wordStart = $start + ($width-$wordLength)/2;
            imagettftext($this->im,26,0,$wordStart,1014,$wordColor,$this->mediumFont,$v);
            $start = $start + $space + $width;
        }
    }

    protected function setQrcode($qrcodeImage)
    {
        $back = imagecreatefrompng('./static/haibao/novrimages/qrcodeBack.png');
        $qrcode = imagecreatefromjpeg($qrcodeImage);

        $backX = (750-imagesx($back))/2;
        $qrcodeX = (750-178)/2;
        imagecopyresized($this->im,$qrcode,$qrcodeX,1057,0,0,178,178,imagesx($qrcode),imagesy($qrcode));
        imagecopy($this->im,$back,$backX,1044,0,0,imagesx($back),imagesy($back));


        imagedestroy($back);
    }

    private function getWordLen($str,$fontSize,$font)
    {
        $box = imagettfbbox($fontSize,0,$font,$str);
        $width = $box[2] - $box[0];
        return $width;
    }
}