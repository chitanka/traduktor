<?php

class CatalogController extends Controller
{
    public function actionIndex($cat_id = 0)
    {
        $category = Category::getCategoryById($cat_id);
        $tree = Category::getTreeFor($category);
        $books = Book::getByCategory($category);

        if (in_array("ajax", $_GET)) $this->renderPartial("catalog_ajax", array("tree" => $tree));
        else {
            $this->render("catalog", array("cat" => $category, "tree" => $tree, "books_dp" => $books));
        }
    }
}
