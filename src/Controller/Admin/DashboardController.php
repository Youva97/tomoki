<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Entity\Order;
use App\Entity\Script;
use App\Entity\Carrier;
use App\Entity\Comment;
use App\Entity\Product;
use App\Entity\Category;
use App\Repository\OrderRepository;
use App\Repository\CommentRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;

class DashboardController extends AbstractDashboardController
{

    private $order;
    private $commentRepository;

    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {
        // return parent::index();

        // Option 1. You can make your dashboard redirect to some common page of your backend
        //
        // $adminUrlGenerator = $this->container->get(AdminUrlGenerator::class);
        // return $this->redirect($adminUrlGenerator->setController(OneOfYourCrudController::class)->generateUrl());

        // Option 2. You can make your dashboard redirect to different pages depending on the user
        //
        // if ('jane' === $this->getUser()->getUsername()) {
        //     return $this->redirect('...');
        // }

        // Option 3. You can render some custom template to display a proper dashboard with widgets, etc.
        // (tip: it's easier if your template extends from @EasyAdmin/page/content.html.twig)
        //
        // redirect to some CRUD controller 
        $routeBuilder = $this->container->get(AdminUrlGenerator::class);
        return $this
            ->redirect(
                $routeBuilder
                    ->setController(OrderCrudController::class)
                    ->generateUrl()
            ); // return $this->render('admin/dashBoard.html.twig', []);
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Tomoki');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');
        yield MenuItem::section('Utilisateurs');
        yield MenuItem::linkToCrud('Utilisateurs', 'fas fa-users', User::class);
        yield MenuItem::linkToCrud('Administrateur', 'fas fa-users', User::class)->setController(AdminCrudController::class);
        yield MenuItem::section('Commandes');
        yield MenuItem::linkToCrud('Transporteurs', 'fas fa-truck', Carrier::class);
        yield MenuItem::linkToCrud('Catégorie', 'fas fa-folder', Category::class);
        yield MenuItem::linkToCrud('Produits', 'fas fa-tags', Product::class)->setController(ProductCrudController::class);
        yield MenuItem::section('Configuration');
        yield MenuItem::linkToCrud('Script', 'far fa-comments', Script::class);
        $nbrsOrderWait = count($this->order->findBy(['statut' => 0]));
        $nbrsOrderOk = count($this->order->findBy(['statut' => 1]));
        yield MenuItem::linkToCrud(
            'Commandes <span style="color:green;font-weight:bold" class="badge badge-success">' . $nbrsOrderOk .
                '</span> <span style="color:red;font-weight:bold" class="badge badge-danger">' . $nbrsOrderWait .
                '</span>',
            'fas fa-shopping-cart',
            Order::class
        );
        $nbrsCommentWait = count($this->commentRepository->findBy(['status' => false]));
        $nbrsCommentOk = count($this->commentRepository->findBy(['status' => true]));
        yield MenuItem::linkToCrud(
            'commentaires <span style="color:green;font-weight:bold" class="badge badge-success">' . $nbrsCommentOk .
                '</span> <span style="color:red;font-weight:bold" class="badge badge-danger">' . $nbrsCommentWait .
                '</span>',
            'fas fa-comments',
            Comment::class
        );
    }

    public function __construct(OrderRepository $order, CommentRepository $commentRepository)
    {
        $this->order = $order;
        $this->commentRepository = $commentRepository;
    }
}
