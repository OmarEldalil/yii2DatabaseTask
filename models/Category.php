<?php


namespace app\models;


use phpDocumentor\Reflection\Types\Null_;
use yii\db\ActiveRecord;

class Category extends ActiveRecord
{
    public static function tableName()
    {
        return 'categories';
    }

    public static function parentLastChild($parentId)
    {
        $parent = self::find()
            ->orderBy('id desc')
            ->where(['parent_id' => $parentId])
            ->one();
        return $parent ? $parent->toArray() : null;

    }
    public static function store($attributes){
        if(!(isset($attributes['name']) || isset($attributes['parent_id']))){
            return null;
        }
        $category = new Category();
        $category->name = $attributes['name'];
        $category->parent_id = $attributes['parent_id'];
        $category->save();
        return $category;
    }
    public static function getNewCategoryBaseNameFromNonRootParent($parentId){
        $parentCategory = self::findOne(['id' => $parentId]);
        $newSubCategoryName = 'SUB ' . $parentCategory->name;
        return $newSubCategoryName;

    }
}
