<?php
class ffmpeg {
    public  $Info,$Location,$Resolution,$Framerate,$SampleRate,$Output,$CRF,$AudioBitrate,$HD,
            $FFMPEG="/usr/bin/ffmpeg",$FFPROBE="/usr/bin/ffprobe";

    private function logFile(){return __DIR__."/ffmpeg.log";}
    private function run($c){$l=$this->logFile();return shell_exec($c." >> ".escapeshellarg($l)." 2>&1");}

    public function Get_Length($E=false){
        $o=shell_exec("$this->FFPROBE -i ".escapeshellarg($this->Location)." 2>&1");
        preg_match('/Duration: (.*?),/',$o,$m);
        if(isset($m[1])&&$m[1]!=="N/A"){list($h,$min,$s)=explode(':',$m[1]);$s=(int)floor($s);$min=(int)$min+((int)$h*60);return $E?$s+$min*60:"$min.$s";}
        return false;
    }

    public function Get_Info(){
        $o=shell_exec("$this->FFPROBE -i ".escapeshellarg($this->Location)." -v quiet -print_format json -show_format -show_streams");
        $this->Info=json_decode($o);
    }

    public function Resize($tH){
        $v=null;foreach($this->Info->streams as $s){if($s->codec_type=="video"){$v=$s;break;}}
        if(!$v)return;
        $vw=$v->width;$vh=$v->height;
        $sar=isset($v->sample_aspect_ratio)?$v->sample_aspect_ratio:"1:1";
        if($sar && strpos($sar,":")!==false){list($sw,$sh)=explode(":",$sar);$vw=round($vw*($sw/$sh));}
        $h=$tH;$w=(int)($vw*($h/$vh));if($w%2)$w++;if($h%2)$h++;
        $this->Resolution=$w."x".$h;$this->HD=($h>=720);
    }

    public function Convert(){
        $f=intval($this->Framerate)?:30;$cr=intval($this->CRF)?:23;$a=escapeshellarg($this->AudioBitrate?:'128k');
        $s=intval($this->SampleRate)?:44100;$o=escapeshellarg($this->Output);
        $c="$this->FFMPEG -y -i ".escapeshellarg($this->Location)." -c:v libx264 -profile:v main -level 3.1 -preset veryfast -s ".escapeshellarg($this->Resolution)." -crf $cr -r $f -pix_fmt yuv420p -c:a aac -b:a $a -ar $s -strict -2 -movflags +faststart -max_muxing_queue_size 102400 $o";
        shell_exec($c);return file_exists($this->Output);
    }

    public function Make_Thumbnails($sec,$URL){
        if(!file_exists($this->Location))return false;
        $vl=$this->Get_Length(true);
        if($sec>=$vl)$sec=max(0,$vl-1);
        $v=null;foreach($this->Info->streams as $s){if($s->codec_type=="video"){$v=$s;break;}}
        if(!$v)return false;
        $ow=$v->width;$oh=$v->height;$asp=$ow/$oh;
        $t=[['dir'=>BASE_DIR."usfi/thmp",'width'=>640,'height'=>360],['dir'=>BASE_DIR."usfi/prvw",'width'=>856,'height'=>480]];
        foreach($t as $x){
            if($asp<(16/9)){$w=$ow;$h=$oh;}else{$sw=$x['width']/$ow;$sh=$x['height']/$oh;$sc=min($sw,$sh);$w=(int)($ow*$sc);$h=(int)($oh*$sc);}
            if($w%2)$w++;if($h%2)$h++;
            $tmp=sys_get_temp_dir()."/$URL.temp.jpg";
            $time=(is_numeric($sec)&&$sec>=0&&$sec<=$vl)?intval($sec):intval($vl*0.50);
           shell_exec("$this->FFMPEG -y -ss $time -i " . escapeshellarg($this->Location) . " -frames:v 1 -update 1 -q:v 2 " . escapeshellarg($tmp));

            if(!file_exists($tmp))return false;
            if(!is_dir($x['dir']))@mkdir($x['dir'],0775,true);
            $U=new Upload($tmp);$U->file_new_name_body=$URL;$U->file_overwrite=true;$U->image_resize=true;$U->image_x=$w;$U->image_y=$h;$U->image_ratio=true;$U->image_ratio_crop=false;$U->image_background_color='#000000';$U->image_convert='jpg';$U->jpeg_quality=85;$U->allowed=['image/jpeg','image/pjpeg','image/png','image/gif','image/bmp','image/x-windows-bmp'];$U->process($x['dir'].'/');
            @unlink($tmp);
        }
        return true;
    }
}
