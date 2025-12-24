<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * Apple model
 *
 * @property integer $id
 * @property string $color
 * @property string $status
 * @property integer $eaten_percent
 * @property integer $fallen_at
 * @property integer $created_at
 * @property integer $updated_at
 */
class Apple extends ActiveRecord
{
    const STATUS_ON_TREE = 'on_tree';
    const STATUS_ON_GROUND = 'on_ground';
    const STATUS_ROTTEN = 'rotten';

    //const TIME_ON_GROUND_TO_BE_ROTTEN = 5 * 3600; // 5 hours
    const TIME_ON_GROUND_TO_BE_ROTTEN = 60; // 1 minute

    public static function tableName(): string
    {
        return 'apple';
    }

    public function behaviors(): array
    {
        return [
            TimestampBehavior::class,
        ];
    }

    public function rules(): array
    {
        return [
            [['color'], 'required'],
            [['eaten_percent'], 'number', 'min' => 0, 'max' => 100],
            [['status'], 'in', 'range' => [self::STATUS_ON_TREE, self::STATUS_ON_GROUND, self::STATUS_ROTTEN]],
            [['color'], 'string', 'max' => 15],
        ];
    }

    public static function create($color = null): Apple
    {
        $colors = ['green', 'yellow', 'red', 'pink'];
        if (!$color) {
            $color = $colors[array_rand($colors)];
        }
        $model = new self();
        $model->color = $color;
        $model->status = self::STATUS_ON_TREE;
        $model->eaten_percent = 0;
        $model->save();

        return $model;
    }

    public function fallToGround()
    {
        if ($this->status != self::STATUS_ON_TREE) {
            throw new \Exception("Apple is not on a tree");
        }
        $this->status = self::STATUS_ON_GROUND;
        $this->fallen_at = time();
        $this->save();
    }

    public function eat($percent)
    {
        $this->checkAndUpdateStatus();

        if ($this->status == self::STATUS_ON_TREE) {
            throw new \Exception("Apple on the tree, can't eat it");
        }
        if ($this->status == self::STATUS_ROTTEN) {
            throw new \Exception("Apple is rotten, can't eat it");
        }

        $remaining = 100 - $this->eaten_percent;
        if ($percent > $remaining) {
            $percent = $remaining;
        }

        $this->eaten_percent += $percent;
        if ($this->eaten_percent >= 100) {
            $this->delete();
        } else {
            $this->save();
        }
    }

    protected function checkAndUpdateStatus()
    {
        if ($this->status == self::STATUS_ON_GROUND) {
            $time_on_ground = time() - $this->updated_at;
            if ($time_on_ground >= self::TIME_ON_GROUND_TO_BE_ROTTEN) {
                $this->status = self::STATUS_ROTTEN;
                $this->save();
            }
        }
    }

    public function delete()
    {
        parent::delete();
    }
}