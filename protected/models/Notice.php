<?php

/**
 * @property integer $id
 * @property integer $user_id
 * @property string  $cdate
 * @property boolean $seen
 * @property integer $typ
 * @property string  $msg
 */
class Notice extends CActiveRecord
{
    const INVITE = 1;
    const JOIN_ACCEPTED = 2;
    const JOIN_DENIED = 3;
    const EXPELLED = 4;
    const JOIN_REQUEST = 5;
    const BANNED = 6;
    const UNBANNED = 7;
    const CROWNED = 8;
    const DEPOSED = 9;
    const CHAPTER_ADDED = 10;
    const CHAPTER_STATUS = 11;

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return "notices";
    }

    public function user($user_id, $new_only = true)
    {
        $c = $this->getDbCriteria();
        $c->mergeWith(array(
            "condition" => "t.user_id = " . intval($user_id),
            "order" => "t.cdate desc"
        ));
        if ($new_only) $c->addCondition("seen = false");

        return $this;
    }

    public function render()
    {
        $domain = "http://" . Yii::app()->params["domain"];
        $m = explode("\n", $this->msg);
        if ($this->typ == self::INVITE) {
            return "<a href='{$domain}/users/{$m[2]}' class='user'>{$m[3]}</a> ви кани в затворената група на превод <a href='{$domain}/book/{$m[0]}'>{$m[1]}</a>.<br /><a href='{$domain}/book/{$m[0]}/invite_accept'>Встъпване</a> или <a href='{$domain}/book/{$m[0]}/invite_decline'>Отказ</a>?";
        } elseif ($this->typ == self::JOIN_ACCEPTED) {
            return "Вашата заявка за участие в групата на превод <a href='{$domain}/book/{$m[0]}'>{$m[1]}</a> е разгледана и одобрена.";
        } elseif ($this->typ == self::JOIN_DENIED) {
            return "Вашата заявка за участие в групата на превод <a href='{$domain}/book/{$m[0]}'>{$m[1]}</a> е разгледана, но уви, отклонена.";
        } elseif ($this->typ == self::EXPELLED) {
            return "Радостна вест: вече не сте в група <a href='{$domain}/book/{$m[0]}'>{$m[1]}</a>.";
        } elseif ($this->typ == self::JOIN_REQUEST) {
            return "<a href='{$domain}/users/{$m[2]}' class='user'>{$m[3]}</a> иска да встъпи в група <a href='{$domain}/book/{$m[0]}'>{$m[1]}</a>. Вие сте модератор и можете да вземете решение в страницата <a href='{$domain}/book/{$m[0]}/members'>участниците на превода</a>.";
        } elseif ($this->typ == self::BANNED) {
            return "Заради някакви прегрешения модераторите или собственикът са ви баннали в групата на превод <a href='{$domain}/book/{$m[0]}'>{$m[1]}</a>. Няма да можете да влезете в този превод, докато те не отменят своето, възможно, недалновидно решение.";
        } elseif ($this->typ == self::UNBANNED) {
            return "Бяхте баннат в групата на превод <a href='{$domain}/book/{$m[0]}'>{$m[1]}</a>, но днес собственикът или модераторите се смилиха над вас и вече можете да го отваряте.";
        } elseif ($this->typ == self::CROWNED) {
            return "Собственикът на превода <a href='{$domain}/book/{$m[0]}'>{$m[1]}</a> ви назначи за модератор.";
        } elseif ($this->typ == self::DEPOSED) {
            return "Собственикът на превода <a href='{$domain}/book/{$m[0]}'>{$m[1]}</a> ви лиши от модераторски пълномощия. Съболезнования.";
        } elseif ($this->typ == self::CHAPTER_ADDED) {
            return "Сложихте отметка в превод <a href='{$domain}/book/{$m[0]}'>{$m[1]}</a> и поискахте да следите промените в него. Та, там току що бе добавена нова глава. \"{$m[3]}\"";
        } elseif ($this->typ == self::CHAPTER_STATUS) {
            return "Сложихте отметка в превод <a href='{$domain}/book/{$m[0]}'>{$m[1]}</a> и поискахте да следите промените в него. Та, статусът на глава \"{$m[3]}\" се промени на \"{$m[4]}\"";
        } else {
            return $this->msg;
        }
    }
}
