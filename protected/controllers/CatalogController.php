<?php

class CatalogController extends Controller
{
    public function actionIndex($cat_id = 0)
    {
        list($cat, $branch) = $this->extractBranch($cat_id);
        $tree = $this->getCategoryTree($branch);
        $books_dp = $this->getBooksByCategory($cat);

        if (in_array("ajax", $_GET)) $this->renderPartial("catalog_ajax", array("tree" => $tree));
        else {
            $this->render("catalog", array("cat" => $cat, "tree" => $tree, "books_dp" => $books_dp));
        }
    }

    /**
     * @param $cat_id
     *
     * @return array
     */
    private function extractBranch($cat_id)
    {
        $cat_id = (int)$cat_id;
        if ($cat_id) {
            $cat = Category::model()->findByPk((int)$cat_id);
            $branch = $cat->mp;
            return array($cat, $branch);
        } else {
            $cat = $branch = null;
            return array($cat, $branch);
        }
    }

    /**
     * @param $branch
     *
     * @return static[]
     */
    private function getCategoryTree($branch)
    {
        $tree = Category::model()->tree($branch)->with("booksCount")->findAll();
        return $tree;
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
