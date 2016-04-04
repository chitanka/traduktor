<?php


/**
 * Class Cacher
 *
 * @package \\${NAMESPACE}
 */
class Cacher
{
    public static function getSearchTop()
    {
        $min_size = 10;
        $max_size = 40;
        $mc_key = "searchTop";

        $html = Yii::app()->cache->get($mc_key);
        if($html != "") return $html;

        $rows = Yii::app()->db->createCommand("
			SELECT lower(request) request, count(distinct ip) as n FROM search_history GROUP BY lower(request) ORDER BY COUNT(DISTINCT ip) DESC LIMIT 50
		")->queryAll();
        if(count($rows) < 5) return "";
        $max_n = 0; $min_n = 100000; $R = array();
        foreach($rows as $row) {
            $row['request'] = strip_tags($row['request']);

            if($row['n'] > $max_n) $max_n = $row['n'];
            if($row['n'] < $min_n) $min_n = $row['n'];

            $R[$row['request']] = $row['n'];
        }

        ksort($R);

        $html = "";
        foreach($R as $request => $n) {
            $size = round($min_size + ($n - $min_n) / ($max_n - $min_n) * ($max_size - $min_size));
            $html .= "<a href='/search/?t=" . urlencode($request) . "&from=stop' style='font-size:{$size}px'>$request</a>\n";
        }

        Yii::app()->cache->set($mc_key, $html, 600);
        return $html;
    }

    /**
     * @return static[]
     */
    public static function getHot()
    {
        $hot_key = sprintf("hot.%d.%d.%d", Yii::app()->user->ini["hot.s_lang"], Yii::app()->user->ini["hot.t_lang"], Yii::app()->user->ini["hot.img"]);
        if (!($hot = Yii::app()->cache->get($hot_key))) {
            $C = new CDbCriteria(array(
                "condition" => "t.ac_read = 'a'",
                "order" => "t.last_tr DESC NULLS LAST",
            ));
            $C->limit = Yii::app()->user->ini["hot.img"] ? 12 : 36;
            if (Yii::app()->user->ini["hot.s_lang"]) $C->addCondition("t.s_lang = " . Yii::app()->user->ini["hot.s_lang"]);
            if (Yii::app()->user->ini["hot.t_lang"]) $C->addCondition("t.t_lang = " . Yii::app()->user->ini["hot.t_lang"]);

            $hot = Book::model()->findAll($C);
            Yii::app()->cache->set($hot_key, $hot, 60);
            return $hot;
        }
        return $hot;
    }
}
