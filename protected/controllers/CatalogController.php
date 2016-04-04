<?php

class CatalogController extends Controller
{
    public function actionIndex($cat_id = 0)
    {
        $category = Category::getCategoryById($cat_id);
        $tree = Category::getTreeFor($category);
        $books_dp = $this->getBooksByCategory($category);

        if (in_array("ajax", $_GET)) $this->renderPartial("catalog_ajax", array("tree" => $tree));
        else {
            $this->render("catalog", array("cat" => $category, "tree" => $tree, "books_dp" => $books_dp));
        }
    }

    /**
     * @param $cat
     *
     * @return \CActiveDataProvider|null
     */
    private function getBooksByCategory($cat)
    {
        if ($cat) {
            $n = count($cat->mp);
            $books_dp = new CActiveDataProvider(Book::model()->with("cat"), array(
                "criteria" => array(
                    "condition" => "cat.mp[1:{$n}] = '{$cat->mpPacked}'",
                    "order" => "t.s_title",
                ),
                "pagination" => array("pageSize" => 50)
            ));
            return $books_dp;
        } else {
            $books_dp = null;
            return $books_dp;
        }
    }

}
