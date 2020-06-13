<?php


namespace app\controllers;


use app\models\Category;
use yii\filters\VerbFilter;
use yii\web\Controller;

use Yii;

class CategoryController extends Controller
{
    private function lastChildNumber($category)
    {
        if (!$category) return 0;
        $matches = [];
        preg_match('#(\d+)$#', $category['name'], $matches);
        if (!count($matches)) return 0;
        return (int)$matches[count($matches) - 1];
    }

    private function handleRootNode()
    {
        $lastParentCategoryChild = Category::parentLastChild(null);
        $lastParentCategoryChildNumber = ((int)$this->lastChildNumber($lastParentCategoryChild)) + 1;
        $category = Category::store([
            'name' => "category {$lastParentCategoryChildNumber}",
            'parent_id' => null
        ]);
        return $this->sendResponseWithCategory($category);

    }

    private function sendResponseWithCategory($category)
    {
        header("HTTP/1.1 201 Created");
        header('Content-Type: application/json');
        return json_encode(['success' => true, 'category' => $category->toArray()]);
    }

    public function actionStorecategory($parentId)
    {
        if ($parentId == 0) {
            return $this->handleRootNode();
        }
        $newCategoryName = Category::getNewCategoryBaseNameFromNonRootParent($parentId);

        $newSubCategoryNumber =(string) ($this->lastChildNumber(Category::parentLastChild($parentId)) + 1);

        $newCategoryName =  "{$newCategoryName}-{$newSubCategoryNumber}";

        $category = Category::store(['name' => $newCategoryName, 'parent_id' => $parentId]);

        return $this->sendResponseWithCategory($category);
    }

    public function actionGetcategory($id)
    {
        $whereExpression = ($id === 'null') ? 'is null' : "= {$id}";

        $categories = Category::find()
            ->where('parent_id ' . $whereExpression)
            ->asArray()
            ->all();
        $results = json_encode($categories);
        header("HTTP/1.1 200 OK");
        header('Content-Type: application/json');
        return $results;
    }

    public function actionSeed()
    {
        Yii::$app->db->
        createCommand()->batchInsert('categories', ['name', 'parent_id'], [
            ['Category 1', null],
            ['category 2', null],
            ['category 3', null],
            ['sub category 1-1', 1],
            ['sub category 1-2', 1],
            ['sub category 2-1', 2],
            ['sub category 3-1', 3],
            ['sub sub category 2-1-1', 2],
            ['sub sub category 3-1-1', 3],
            ['sub sub category 1-2-1', 5],
        ])->execute();
        return $this->redirect(['site/index']);
    }
}
