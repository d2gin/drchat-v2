<?php

namespace Database\Seeders;

use App\Api\Models\ChatExpression;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class ExpressionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $list   = [
            [
                'name' => '2_07',
                'path' => 'static/expression/2_07.png',
                'code' => '2_07',
            ],
            [
                'name' => '2_11',
                'path' => 'static/expression/2_11.png',
                'code' => '2_11',
            ],
            [
                'name' => '666',
                'path' => 'static/expression/666.png',
                'code' => '666',
            ],
            [
                'name' => 'Boring',
                'path' => 'static/expression/Boring.png',
                'code' => 'Boring',
            ],
            [
                'name' => 'Broken',
                'path' => 'static/expression/Broken.png',
                'code' => 'Broken',
            ],
            [
                'name' => 'KeepFighting',
                'path' => 'static/expression/KeepFighting.png',
                'code' => 'KeepFighting',
            ],
            [
                'name' => 'LetMeSee',
                'path' => 'static/expression/LetMeSee.png',
                'code' => 'LetMeSee',
            ],
            [
                'name' => 'aixin',
                'path' => 'static/expression/aixin.png',
                'code' => 'aixin',
            ],
            [
                'name' => 'aoman',
                'path' => 'static/expression/aoman.png',
                'code' => 'aoman',
            ],
            [
                'name' => 'baiyan',
                'path' => 'static/expression/baiyan.png',
                'code' => 'baiyan',
            ],
            [
                'name' => 'baoquan',
                'path' => 'static/expression/baoquan.png',
                'code' => 'baoquan',
            ],
            [
                'name' => 'bianbian',
                'path' => 'static/expression/bianbian.png',
                'code' => 'bianbian',
            ],
            [
                'name' => 'bishi',
                'path' => 'static/expression/bishi.png',
                'code' => 'bishi',
            ],
            [
                'name' => 'bizui',
                'path' => 'static/expression/bizui.png',
                'code' => 'bizui',
            ],
            [
                'name' => 'cahan',
                'path' => 'static/expression/cahan.png',
                'code' => 'cahan',
            ],
            [
                'name' => 'caidao',
                'path' => 'static/expression/caidao.png',
                'code' => 'caidao',
            ],
            [
                'name' => 'chigua',
                'path' => 'static/expression/chigua.png',
                'code' => 'chigua',
            ],
            [
                'name' => 'ciya',
                'path' => 'static/expression/ciya.png',
                'code' => 'ciya',
            ],
            [
                'name' => 'daku',
                'path' => 'static/expression/daku.png',
                'code' => 'daku',
            ],
            [
                'name' => 'dalian',
                'path' => 'static/expression/dalian.png',
                'code' => 'dalian',
            ],
            [
                'name' => 'dangao',
                'path' => 'static/expression/dangao.png',
                'code' => 'dangao',
            ],
            [
                'name' => 'deyi',
                'path' => 'static/expression/deyi.png',
                'code' => 'deyi',
            ],
            [
                'name' => 'diaoxie',
                'path' => 'static/expression/diaoxie.png',
                'code' => 'diaoxie',
            ],
            [
                'name' => 'emm',
                'path' => 'static/expression/emm.png',
                'code' => 'emm',
            ],
            [
                'name' => 'fa',
                'path' => 'static/expression/fa.png',
                'code' => 'fa',
            ],
            [
                'name' => 'fadai',
                'path' => 'static/expression/fadai.png',
                'code' => 'fadai',
            ],
            [
                'name' => 'fadou',
                'path' => 'static/expression/fadou.png',
                'code' => 'fadou',
            ],
            [
                'name' => 'fanu',
                'path' => 'static/expression/fanu.png',
                'code' => 'fanu',
            ],
            [
                'name' => 'fendou',
                'path' => 'static/expression/fendou.png',
                'code' => 'fendou',
            ],
            [
                'name' => 'fu',
                'path' => 'static/expression/fu.png',
                'code' => 'fu',
            ],
            [
                'name' => 'ganga',
                'path' => 'static/expression/ganga.png',
                'code' => 'ganga',
            ],
            [
                'name' => 'gouyin',
                'path' => 'static/expression/gouyin.png',
                'code' => 'gouyin',
            ],
            [
                'name' => 'guzhang',
                'path' => 'static/expression/guzhang.png',
                'code' => 'guzhang',
            ],
            [
                'name' => 'haixiu',
                'path' => 'static/expression/haixiu.png',
                'code' => 'haixiu',
            ],
            [
                'name' => 'han',
                'path' => 'static/expression/han.png',
                'code' => 'han',
            ],
            [
                'name' => 'hanxiao',
                'path' => 'static/expression/hanxiao.png',
                'code' => 'hanxiao',
            ],
            [
                'name' => 'haode',
                'path' => 'static/expression/haode.png',
                'code' => 'haode',
            ],
            [
                'name' => 'haqian',
                'path' => 'static/expression/haqian.png',
                'code' => 'haqian',
            ],
            [
                'name' => 'hehe',
                'path' => 'static/expression/hehe.png',
                'code' => 'hehe',
            ],
            [
                'name' => 'heiha',
                'path' => 'static/expression/heiha.png',
                'code' => 'heiha',
            ],
            [
                'name' => 'hongbao',
                'path' => 'static/expression/hongbao.png',
                'code' => 'hongbao',
            ],
            [
                'name' => 'huaixiao',
                'path' => 'static/expression/huaixiao.png',
                'code' => 'huaixiao',
            ],
            [
                'name' => 'hurt',
                'path' => 'static/expression/hurt.png',
                'code' => 'hurt',
            ],
            [
                'name' => 'ji',
                'path' => 'static/expression/ji.png',
                'code' => 'ji',
            ],
            [
                'name' => 'jianxiao',
                'path' => 'static/expression/jianxiao.png',
                'code' => 'jianxiao',
            ],
            [
                'name' => 'jiayou',
                'path' => 'static/expression/jiayou.png',
                'code' => 'jiayou',
            ],
            [
                'name' => 'jiayoujiayou',
                'path' => 'static/expression/jiayoujiayou.png',
                'code' => 'jiayoujiayou',
            ],
            [
                'name' => 'jingkong',
                'path' => 'static/expression/jingkong.png',
                'code' => 'jingkong',
            ],
            [
                'name' => 'jingya',
                'path' => 'static/expression/jingya.png',
                'code' => 'jingya',
            ],
            [
                'name' => 'jiong',
                'path' => 'static/expression/jiong.png',
                'code' => 'jiong',
            ],
            [
                'name' => 'jizhi',
                'path' => 'static/expression/jizhi.png',
                'code' => 'jizhi',
            ],
            [
                'name' => 'kafei',
                'path' => 'static/expression/kafei.png',
                'code' => 'kafei',
            ],
            [
                'name' => 'kelian',
                'path' => 'static/expression/kelian.png',
                'code' => 'kelian',
            ],
            [
                'name' => 'koubi',
                'path' => 'static/expression/koubi.png',
                'code' => 'koubi',
            ],
            [
                'name' => 'kuaikule',
                'path' => 'static/expression/kuaikule.png',
                'code' => 'kuaikule',
            ],
            [
                'name' => 'kulou',
                'path' => 'static/expression/kulou.png',
                'code' => 'kulou',
            ],
            [
                'name' => 'kun',
                'path' => 'static/expression/kun.png',
                'code' => 'kun',
            ],
            [
                'name' => 'lazhu',
                'path' => 'static/expression/lazhu.png',
                'code' => 'lazhu',
            ],
            [
                'name' => 'liuhan',
                'path' => 'static/expression/liuhan.png',
                'code' => 'liuhan',
            ],
            [
                'name' => 'liulei',
                'path' => 'static/expression/liulei.png',
                'code' => 'liulei',
            ],
            [
                'name' => 'meigui',
                'path' => 'static/expression/meigui.png',
                'code' => 'meigui',
            ],
            [
                'name' => 'nanguo',
                'path' => 'static/expression/nanguo.png',
                'code' => 'nanguo',
            ],
            [
                'name' => 'ohuo',
                'path' => 'static/expression/ohuo.png',
                'code' => 'ohuo',
            ],
            [
                'name' => 'ok',
                'path' => 'static/expression/ok.png',
                'code' => 'ok',
            ],
            [
                'name' => 'piezui',
                'path' => 'static/expression/piezui.png',
                'code' => 'piezui',
            ],
            [
                'name' => 'pijiu',
                'path' => 'static/expression/pijiu.png',
                'code' => 'pijiu',
            ],
            [
                'name' => 'qiang',
                'path' => 'static/expression/qiang.png',
                'code' => 'qiang',
            ],
            [
                'name' => 'qiaoda',
                'path' => 'static/expression/qiaoda.png',
                'code' => 'qiaoda',
            ],
            [
                'name' => 'qinqin',
                'path' => 'static/expression/qinqin.png',
                'code' => 'qinqin',
            ],
            [
                'name' => 'quantou',
                'path' => 'static/expression/quantou.png',
                'code' => 'quantou',
            ],
            [
                'name' => 'ruo',
                'path' => 'static/expression/ruo.png',
                'code' => 'ruo',
            ],
            [
                'name' => 'se',
                'path' => 'static/expression/se.png',
                'code' => 'se',
            ],
            [
                'name' => 'shehuishehui',
                'path' => 'static/expression/shehuishehui.png',
                'code' => 'shehuishehui',
            ],
            [
                'name' => 'shengli',
                'path' => 'static/expression/shengli.png',
                'code' => 'shengli',
            ],
            [
                'name' => 'shui',
                'path' => 'static/expression/shui.png',
                'code' => 'shui',
            ],
            [
                'name' => 'sigh',
                'path' => 'static/expression/sigh.png',
                'code' => 'sigh',
            ],
            [
                'name' => 'sui',
                'path' => 'static/expression/sui.png',
                'code' => 'sui',
            ],
            [
                'name' => 'sweat',
                'path' => 'static/expression/sweat.png',
                'code' => 'sweat',
            ],
            [
                'name' => 'taiyang',
                'path' => 'static/expression/taiyang.png',
                'code' => 'taiyang',
            ],
            [
                'name' => 'tiana',
                'path' => 'static/expression/tiana.png',
                'code' => 'tiana',
            ],
            [
                'name' => 'tiaopi',
                'path' => 'static/expression/tiaopi.png',
                'code' => 'tiaopi',
            ],
            [
                'name' => 'tiaotiao',
                'path' => 'static/expression/tiaotiao.png',
                'code' => 'tiaotiao',
            ],
            [
                'name' => 'touxiao',
                'path' => 'static/expression/touxiao.png',
                'code' => 'touxiao',
            ],
            [
                'name' => 'tu',
                'path' => 'static/expression/tu.png',
                'code' => 'tu',
            ],
            [
                'name' => 'wa',
                'path' => 'static/expression/wa.png',
                'code' => 'wa',
            ],
            [
                'name' => 'wangchai',
                'path' => 'static/expression/wangchai.png',
                'code' => 'wangchai',
            ],
            [
                'name' => 'weiqu',
                'path' => 'static/expression/weiqu.png',
                'code' => 'weiqu',
            ],
            [
                'name' => 'woshou',
                'path' => 'static/expression/woshou.png',
                'code' => 'woshou',
            ],
            [
                'name' => 'wulian',
                'path' => 'static/expression/wulian.png',
                'code' => 'wulian',
            ],
            [
                'name' => 'xigua',
                'path' => 'static/expression/xigua.png',
                'code' => 'xigua',
            ],
            [
                'name' => 'xinsui',
                'path' => 'static/expression/xinsui.png',
                'code' => 'xinsui',
            ],
            [
                'name' => 'xu',
                'path' => 'static/expression/xu.png',
                'code' => 'xu',
            ],
            [
                'name' => 'ye',
                'path' => 'static/expression/ye.png',
                'code' => 'ye',
            ],
            [
                'name' => 'yinxian',
                'path' => 'static/expression/yinxian.png',
                'code' => 'yinxian',
            ],
            [
                'name' => 'yiwen',
                'path' => 'static/expression/yiwen.png',
                'code' => 'yiwen',
            ],
            [
                'name' => 'yongbao',
                'path' => 'static/expression/yongbao.png',
                'code' => 'yongbao',
            ],
            [
                'name' => 'youhengheng',
                'path' => 'static/expression/youhengheng.png',
                'code' => 'youhengheng',
            ],
            [
                'name' => 'youxian',
                'path' => 'static/expression/youxian.png',
                'code' => 'youxian',
            ],
            [
                'name' => 'yueliang',
                'path' => 'static/expression/yueliang.png',
                'code' => 'yueliang',
            ],
            [
                'name' => 'yukuai',
                'path' => 'static/expression/yukuai.png',
                'code' => 'yukuai',
            ],
            [
                'name' => 'yun',
                'path' => 'static/expression/yun.png',
                'code' => 'yun',
            ],
            [
                'name' => 'zaininmadejian',
                'path' => 'static/expression/zaininmadejian.png',
                'code' => 'zaininmadejian',
            ],
            [
                'name' => 'zhadan',
                'path' => 'static/expression/zhadan.png',
                'code' => 'zhadan',
            ],
            [
                'name' => 'zhouma',
                'path' => 'static/expression/zhouma.png',
                'code' => 'zhouma',
            ],
            [
                'name' => 'zhoumei',
                'path' => 'static/expression/zhoumei.png',
                'code' => 'zhoumei',
            ],
            [
                'name' => 'zhuakuang',
                'path' => 'static/expression/zhuakuang.png',
                'code' => 'zhuakuang',
            ],
            [
                'name' => 'zhuanquan',
                'path' => 'static/expression/zhuanquan.png',
                'code' => 'zhuanquan',
            ],
            [
                'name' => 'zhutou',
                'path' => 'static/expression/zhutou.png',
                'code' => 'zhutou',
            ],
            [
                'name' => 'zuichun',
                'path' => 'static/expression/zuichun.png',
                'code' => 'zuichun',
            ],
            [
                'name' => 'zuohengheng',
                'path' => 'static/expression/zuohengheng.png',
                'code' => 'zuohengheng',
            ],
        ];
        $domain = Config::get('chat.expression_url') . '/'; // 域名
        DB::transaction(function () use ($domain, $list) {
            foreach ($list as $row) {
                $row['path'] = $domain . $row['path'];
                $model       = new ChatExpression($row);
                $model->save();
            }
        });
    }
}
