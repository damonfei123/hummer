<?php
namespace App\controller\cli\data;

use App\opt\TecLoginLog;
use App\helper\IP;
use Hummer\Component\Helper\Arr;
use App\system\controller\Cli_Base;

class C_Beer extends Cli_Base{

    public function __before__()
    {
    }

    public function go()
    {
        $Redis = \Redis();
        $Cache = CTX()->CacheFile();
        $Cache->set('name', 'damon');
        return $Redis->get('name');
    }

    public function actionGenerateData()
    {
        //先生成对应的uuid
        ini_set('memory_limit', -1);
        $BeerSn = M('beer_sn_1');
        $iFirst = 35000;
        $iSecond = 100;
        for ($i = 0; $i < $iFirst; $i++) {
            $data = array();
            echo '第' . $i . '次循环'."\n";
            for ($j = 0; $j < $iSecond; $j++) {
                $sUUID = $BeerSn->query('select uuid() as uuid')[0]['uuid'];
                $data[$j] = array(
                    'sn'    => $sUUID,
                    'crc32sn'=> crc32($sUUID),
                    'score' => 30
                );
            }
            $BeerSn->batchAdd($data);
        }
    }
}
