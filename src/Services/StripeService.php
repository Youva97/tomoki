<?php
namespace App\Services;


use Stripe\Stripe;
use App\Entity\Order;
use App\Entity\User;
use Stripe\Checkout\Session;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

class StripeService extends AbstractController
{
    public function stripePayment(#[CurrentUser] ? User $user, Order $order, $cartComplete)
    {

        $product_for_stripe = [];

        foreach ($cartComplete as $product) {
            $product_for_stripe[] = [
                'price_data' => [
                    'currency' => 'eur',
                    'product_data' => [
                        'name' => $product['product']->getName(),
                        'images' => [
                            $_SERVER['HTTP_ORIGIN'] . '/uploads/' . $product['product']->getIllustration()
                        ],
                    ],
                    'unit_amount' => $product['product']->getPrice(), // Prix en centimes (converti en centimes)
                ],
                'quantity' => $product['quantity'],
            ];
        }

        $product_for_stripe[] = [
            'price_data' => [
                'currency' => 'eur',
                'product_data' => [
                    'name' => $order->getCarrier()->getName(),
                ],
                'unit_amount' => $order->getCarrier()->getPrice(),
            ],
            'quantity' => 1,
        ];

        Stripe::setApiKey($this->getParameter('STRIPE_KEY'));
        $YOUR_DOMAIN = 'http://localhost:8000';
        $checkout_session = Session::create([
            'payment_method_types' => ['card'],
            'line_items' => $product_for_stripe,
            'customer_email' => $user->getEmail(),
            'mode' => 'payment',
            'success_url' => $YOUR_DOMAIN . '/commande/merci/{CHECKOUT_SESSION_ID}',
            'cancel_url' => $YOUR_DOMAIN . '/commande/dommage/{CHECKOUT_SESSION_ID}'
        ]);

        return [
            $checkout_session->url,
            $checkout_session->id, // Exemple de donnée supplémentaire que vous pourriez retourner
            // Ajoutez d'autres données si nécessaire
        ];
    }
}
