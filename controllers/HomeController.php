<?php
class HomeController {
    public function index() {
        $activityModel = new Activity();
        $categoryModel = new Category();

        $activitesRecentes = $activityModel->getRecent(6);
        $categories = $categoryModel->getAll();
        $totalActivites = $activityModel->countActive();

        $userModel = new User();
        $totalUtilisateurs = $userModel->countAll();

        include __DIR__ . '/../views/layout/header.php';
        include __DIR__ . '/../views/home.php';
        include __DIR__ . '/../views/layout/footer.php';
    }
}
