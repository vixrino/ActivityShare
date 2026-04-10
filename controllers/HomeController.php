<?php
class HomeController {
    public function index() {
        $activityModel = new Activity();
        $categoryModel = new Category();
        $userModel = new User();

        $activitesRecentes = $activityModel->getRecent(6);
        $categories = $categoryModel->getAll();
        $totalActivites = $activityModel->countActive();
        $totalUtilisateurs = $userModel->countAll();

        include __DIR__ . '/../views/layout/header.php';
        include __DIR__ . '/../views/home.php';
        include __DIR__ . '/../views/layout/footer.php';
    }
}
